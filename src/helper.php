<?php

declare(strict_types=1);
/**
 * This file is part of stru/stru-hyperf-auth.
 */
use Hyperf\Utils\ApplicationContext;
use Stru\StruHyperfAuth\AuthManager;

if (! function_exists('auth')) {

    function auth(?string $guard = null)
    {
        $auth = ApplicationContext::getContainer()->get(AuthManager::class);

        if (is_null($guard)) {
            return $auth;
        }

        return $auth->guard($guard);
    }
}

if (! function_exists('auth_user')) {

    function auth_user(?string $guard = null)
    {
        $auth = ApplicationContext::getContainer()->get(AuthManager::class);
        return $auth->guard('session')->user();
    }
}
