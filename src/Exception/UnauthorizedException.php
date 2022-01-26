<?php

declare(strict_types=1);
/**
 * This file is part of stru/stru-hyperf-auth.
 */
namespace Stru\StruHyperfAuth\Exception;

use Stru\StruHyperfAuth\Guard;

class UnauthorizedException extends AuthException
{
    protected $guards;

    protected $statusCode = 401;

    public function __construct(string $message, Guard $guard = null, $redirectTo = null)
    {
        parent::__construct($message, $redirectTo);
        $this->guards = $guard;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }
}
