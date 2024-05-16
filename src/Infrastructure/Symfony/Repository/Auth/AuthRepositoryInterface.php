<?php

namespace Infrastructure\Symfony\Repository\Auth;

interface AuthRepositoryInterface 
{
    public function newLoginAttemptFailed(int $userId, ?string $clientIp): void;
    public function getThreeLastLoginAttemptsFailed(int $userId, ?string $clientIp);
}