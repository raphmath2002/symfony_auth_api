<?php

namespace Domain\Response\User;

use Domain\Response\GenericResponse;

class AddNewUserResponse extends GenericResponse
{
    public function userCreated()
    {
        $this->message = "User succefully created";
        $this->statusCode = parent::HTTP_CREATED;
    }

    public function setData($user)
    {
        parent::setData([
            'user' => [
                "first_name" => $user->first_name,
                "last_name" => $user->last_name,
                "email" => $user->email,
                "roles" => $user->getRoles()
            ]
        ]);
    }
}