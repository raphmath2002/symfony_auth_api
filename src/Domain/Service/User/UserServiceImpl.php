<?php

namespace Domain\Service\User;

use Domain\Request\AddNewUserRequest;
use Domain\Request\UpdateUserRequest;
use Domain\Response\User\AddNewUserResponse;
use Domain\Response\User\GetUserByIdResponse;
use Domain\Response\User\UpdateUserResponse;
use Infrastructure\Symfony\Repository\User\UserRepositoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserServiceImpl implements UserServiceInterface
{

    public function __construct(
        protected ValidatorInterface $validator,
        protected UserRepositoryInterface $userRepository
    ) {}

    public function addNewUser(AddNewUserRequest $request): AddNewUserResponse
    {
        $response = new AddNewUserResponse();

        $newUser = $request->getNewUser();

        $errors = $this->validator->validate($newUser);

        if (count($errors) > 0) {
            $response->setValidationError();

            foreach ($errors as $error) {
                $response->addFieldErrorMessage(
                    $error->getPropertyPath(),
                    $error->getMessage()
                );
            }
            return $response;
        }

        try {

            $hashedPass = password_hash($newUser->password, PASSWORD_BCRYPT, ['cost' => 10]);

            $newUser->password = $hashedPass;

            $this->userRepository->storeUser($newUser);
            
        } catch (\Exception $e) {
            $response->setException($e);
            return $response;
        }

        $response->setData($newUser);
        $response->userCreated();
        return $response;
    } 

    public function getUserById(int $userId): GetUserByIdResponse
    {
        $response = new GetUserByIdResponse();

        try {
            $user = $this->userRepository->getUserById($userId);

            if(is_null($user)) {
                $response->notFound();
                return $response;
            }

            $response->fetchOk();
            $response->setData($user);

        } catch (\Exception $e) {
            $response->setException($e);
            return $response;
        }

        return $response;
    }

    public function updateUser(UpdateUserRequest $request): UpdateUserResponse
    {
        $response = new UpdateUserResponse();

        [$id, $updatedData] = array_values($request->getData());

        $user = $this->userRepository->getUserById($id);

        if (is_null($user)) {
            $response->notFound();
            return $response;
        }

        array_key_exists('first_name', $updatedData) && $user->first_name = $updatedData['first_name'];
        array_key_exists('last_name', $updatedData) && $user->last_name = $updatedData['last_name'];
        array_key_exists('email', $updatedData) && $user->email = $updatedData['email'];
        
        // Je sais que c'est pas bien de faire ça comme ça mais bon
        if(array_key_exists('password', $updatedData)) {
            $hashedPass = password_hash($updatedData['password'], PASSWORD_BCRYPT, ['cost' => 10]);
            $user->password = $hashedPass;
        }

        array_key_exists('roles', $updatedData) && $user->setRoles($updatedData['roles']);

        array_key_exists('status', $updatedData) && $user->status = $updatedData['status'];


        try {
            $this->userRepository->updateUser($user);
            $response->userUpdated();
        } catch (\Exception $e) {
            $response->setException($e);
            return $response;
        }

        return $response;
    }
}