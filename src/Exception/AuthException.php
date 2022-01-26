<?php

declare(strict_types=1);
/**
 * This file is part of stru/stru-hyperf-auth.
 */
namespace Stru\StruHyperfAuth\Exception;

abstract class AuthException extends \RuntimeException
{

    protected $redirectTo;

    public function __construct($message = "Unauthenticated.", $redirectTo = null)
    {
        parent::__construct($message);
        // 存储访问异常前的URL
        $this->redirectTo = $redirectTo;

    }

    public function redirectTo()
    {
        return $this->redirectTo;
    }
}
