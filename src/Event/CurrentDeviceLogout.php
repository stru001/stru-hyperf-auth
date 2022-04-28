<?php

declare(strict_types=1);

namespace Stru\StruHyperfAuth\Event;

use Stru\StruHyperfAuth\Authenticatable;

class CurrentDeviceLogout
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
     * Create a new event instance.
     */
    public function __construct(string $guard, Authenticatable $user)
    {
        $this->user = $user;
        $this->guard = $guard;
    }
}
