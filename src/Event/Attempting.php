<?php

declare(strict_types=1);

namespace Stru\StruHyperfAuth\Event;

class Attempting
{
    /**
     * The authentication guard name.
     *
     * @var string
     */
    public $guard;

    /**
     * The credentials for the user.
     *
     * @var array
     */
    public $credentials;

    /**
     * Indicates if the user should be "remembered".
     *
     * @var bool
     */
    public $remember;

    /**
     * Create a new event instance.
     */
    public function __construct(string $guard, array $credentials, bool $remember)
    {
        $this->guard = $guard;
        $this->remember = $remember;
        $this->credentials = $credentials;
    }
}
