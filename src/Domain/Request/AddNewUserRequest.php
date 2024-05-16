<?php

namespace Domain\Request;

use Domain\Entity\User;

class AddNewUserRequest
{
    protected ?User $user = null;

    public function setNewUser(User $user) {
        $this->user = $user;
    }

    public function getNewUser(): ?User
    {
        return $this->user;
    }
}