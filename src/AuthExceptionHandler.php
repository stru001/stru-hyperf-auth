<?php

declare(strict_types=1);
/**
 * This file is part of stru/stru-hyperf-auth.
 */
namespace Stru\StruHyperfAuth;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Stru\StruHyperfAuth\Exception\UnauthorizedException;
use Throwable;

class AuthExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->stopPropagation();
        return $response->withHeader('location','/auth/login')
            ->withStatus(301)
            ->withBody(new SwooleStream(''));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof UnauthorizedException;
    }
}
