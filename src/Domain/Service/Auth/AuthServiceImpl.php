<?php

namespace Domain\Service\Auth;

use Domain\Entity\User;
use Domain\Request\LoginRequest;
use Domain\Response\Auth\LoginResponse;
use Infrastructure\Symfony\Repository\Auth\AuthRepositoryInterface;
use Infrastructure\Symfony\Repository\User\UserRepositoryInterface;

class AuthServiceImpl implements AuthServiceInterface
{

    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected AuthRepositoryInterface $authRepository,
        protected string $appSecret
    ) {
    }

    public function login(LoginRequest $request): LoginResponse
    {

        $response = new LoginResponse();

        $creds = $request->getLoginCredentials();

        $clientIp = $request->getClientIp();

        if (
            ($creds->email === null || trim($creds->email) === '') ||
            ($creds->password === null || trim($creds->password) === '')
        ) {
            $response->setValidationError();
            $response->addFieldErrorMessage('credentials', 'please provide a valid email:password combo');
            return $response;
        }

        try {
            $user = $this->userRepository->getUserByEmail($creds->email);

            if (is_null($user)) {
                $response->userNotFound();
                return $response;
            }


            if (password_verify($creds->password, $user->password)) {
                $access_jwt = $this->generateJWT($user, "access");
                $refresh_jwt = $this->generateJWT($user, "refresh");

                $response->logged($access_jwt, $refresh_jwt);
            } else {

                $this->authRepository->newLoginAttemptFailed($user->id, $clientIp);

                $needToBeLocked = $this->isUserNeedToBeLocked($user->id);

                //$this->authRepository->lockUser($user->id);



                $response->loginError();
            };
        } catch (\Exception $e) {
            $response->setException($e);
            return $response;
        }

        return $response;
    }

    public function refreshToken(string $refreshToken): LoginResponse
    {
        $response = new LoginResponse();

        [$header, $payload, $signatureFromUser] = explode(".", $refreshToken);

        $signatureFromSystem = hash_hmac('sha256', "$header.$payload", $this->appSecret);

        if ($signatureFromSystem === $signatureFromUser) {
            $decodedPayload = base64_decode($payload);

            if (json_validate($decodedPayload)) {
                $decodedPayload = json_decode($decodedPayload, true);

                $user = $this->userRepository->getUserByEmail($decodedPayload['user_email']);

                if (!is_null($user) && time() < $decodedPayload['expire_at'] && $decodedPayload['type'] === "refresh") {
                    $access_jwt = $this->generateJWT($user, "access");
                    $refresh_jwt = $this->generateJWT($user, "refresh");

                    $response->logged($access_jwt, $refresh_jwt);
                    return $response;
                }
            }
        }

        $response->setMessage("Invalid or non-existent token");
        $response->statusCode = 404;
        return $response;
    }

    private function isUserLocked(int $userId): bool
    {
    }

    private function isUserNeedToBeLocked(int $userId): bool
    {
        $lastThreeFailedLoginAttempts = $this->authRepository->getThreeLastLoginAttemptsFailed($userId);

        if (count($lastThreeFailedLoginAttempts) == 3) {
            $firstAttemptAt = $lastThreeFailedLoginAttempts[2]['created_at'];
            $lastAttemptAt = $lastThreeFailedLoginAttempts[0]['created_at'];

            $firstAttemptAtTimestamp = strtotime($firstAttemptAt);
            $lastAttemptAtTimestamp = strtotime($lastAttemptAt);

            $failTimeRangeMinutes = ($lastAttemptAtTimestamp - $firstAttemptAtTimestamp) / 60;

            if ($failTimeRangeMinutes > 5) {
                return true;
            }
        }

        return false;
    }

    private function generateJWT(User $user, string $type): object
    {
        $header = base64_encode(json_encode([
            "alg" => "HS256",
            "typ" => "JWT"
        ]));

        $now = time();

        $exp_factor = 60 * ($type === "access" ? 60 : 120);

        $exp = $now + $exp_factor;

        $payload = base64_encode(json_encode([
            "type" => $type,
            "user_email" => $user->email,
            "created_at" => $now,
            "expire_at" => $exp
        ]));

        $signature = hash_hmac('sha256', "$header.$payload", $this->appSecret);

        $jwt = "$header.$payload.$signature";

        return (object) [
            "token" => $jwt,
            "expire_at" => new \DateTime(date('Y-m-d H:i:s', $exp), new \DateTimeZone('GMT+2'))
        ];
    }
}
