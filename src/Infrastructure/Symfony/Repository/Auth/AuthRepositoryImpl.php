<?php

namespace Infrastructure\Symfony\Repository\Auth;

use Doctrine\ORM\EntityManagerInterface;
use Infrastructure\Symfony\Repository\User\UserRepositoryInterface;

class AuthRepositoryImpl implements AuthRepositoryInterface
{
    public function __construct(
        protected EntityManagerInterface $em,
        protected UserRepositoryInterface $userRepository
    ) {
    }

    public function newLoginAttemptFailed(int $userId, ?string $clientIp): void
    {
        $conn = $this->em->getConnection();

        $sql = "INSERT INTO auth_failures (`user_id`,`client_ip`) VALUES (:userId, :clientIp);";

        $conn->executeQuery($sql, ["userId" => $userId, "clientIp" => $clientIp]);
    }

    public function getThreeLastLoginAttemptsFailed(int $userId, ?string $clientIp)
    {
        $conn = $this->em->getConnection();

        //TODO.ERROR ICI

        // $sql = "SELECT `created_at` FROM auth_failures WHERE `user_id` = :userId ".
        //     ()
        // ." ORDER BY `created_at` DESC LIMIT 3;";

        $lastAttempts = $conn->executeQuery($sql, ["userId" => $userId]);

        return $lastAttempts->fetchAllAssociative();
    }
}
