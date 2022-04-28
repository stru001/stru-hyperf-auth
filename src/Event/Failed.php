<?php

declare(strict_types=1);

namespace Stru\StruHyperfAuth\Event;

use Stru\StruHyperfAuth\Authenticatable;

class Failed
{
    /**
     * The authentication guard name.
     *
     * @var string
     */
    public $guard;

    /**
     * The user the attempter was trying to authenticate as.
     *
     * @var null|Authenticatable
     */
    public $user;

    /**
     * The credentials provided by the attempter.
     *
     * @var array
     */
    public $credentials;

    /**
     * Create a new event instance.
     */
    public function __construct(string $guard, ?Authenticatable $user, array $credentials)
    {
        $this->user = $user;
        $this->guard = $guard;
        $this->credentials = $credentials;
    }
}
