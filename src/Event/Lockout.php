<?php

declare(strict_types=1);

namespace Stru\StruHyperfAuth\Event;

use Psr\Http\Message\ServerRequestInterface;

class Lockout
{
    /**
     * The throttled request.
     *
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    public $request;

    /**
     * Create a new event instance.
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }
}
