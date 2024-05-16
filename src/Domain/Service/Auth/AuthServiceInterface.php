<?php

namespace Domain\Service\Auth;

use Domain\Request\LoginRequest;
use Domain\Response\Auth\LoginResponse;

interface AuthServiceInterface
{
    public function login(LoginRequest $request): LoginResponse;
    public function refreshToken(string $refreshToken): LoginResponse;
}