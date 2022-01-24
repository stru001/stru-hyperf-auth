<?php

declare(strict_types=1);
/**
 * This file is part of stru/stru-hyperf-auth.
 */
namespace Stru\StruHyperfAuth\Exception;

use Stru\StruHyperfAuth\Guard;
use Throwable;

class UnauthorizedException extends AuthException
{
    protected $guard;

    protected $statusCode = 401;

    public function __construct(string $message, Guard $guard = null, Throwable $previous = null)
    {
        parent::__construct($message, 401, $previous);
        $this->guard = $guard;
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
