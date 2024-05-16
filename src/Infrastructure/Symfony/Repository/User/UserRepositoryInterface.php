<?php

namespace Infrastructure\Symfony\Repository\User;

use Domain\Entity\User;

interface UserRepositoryInterface
{
    public function storeUser(User $newUser): User;
    public function getUserByEmail(string $email): ?User;
    public function getUserById(int $userId): ?User;
    public function updateUser(User $updatedUser);
}