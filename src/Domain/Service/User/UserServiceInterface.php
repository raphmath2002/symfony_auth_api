<?php

namespace Domain\Service\User;

use Domain\Request\AddNewUserRequest;
use Domain\Request\UpdateUserRequest;
use Domain\Response\User\AddNewUserResponse;
use Domain\Response\User\GetUserByIdResponse;
use Domain\Response\User\UpdateUserResponse;

interface UserServiceInterface
{
    public function addNewUser(AddNewUserRequest $request): AddNewUserResponse;
    public function getUserById(int $userId): GetUserByIdResponse;
    public function updateUser(UpdateUserRequest $request): UpdateUserResponse;
}