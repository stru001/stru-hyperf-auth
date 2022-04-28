<?php

declare(strict_types=1);

namespace Stru\StruHyperfAuth\Event;

use Stru\StruHyperfAuth\Authenticatable;

class Login
{
    /**
     * The authentication guard name.
     *
     * @var string
     */
    public $guard;

    /**
     * The authenticated user.
     *
     * @var Authenticatable
     */
    public $user;

    /**
     * Indicates if the user should be "remembered".
     *
     * @var bool
     */
    public $remember;

    /**
     * Create a new event instance.
     */
    public function __construct(string $guard, Authenticatable $user, bool $remember)
    {
        $this->guard = $guard;
        $this->user = $user;
        $this->remember = $remember;
    }
}
