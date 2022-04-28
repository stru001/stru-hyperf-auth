<?php

declare(strict_types=1);

namespace Stru\StruHyperfAuth\Event;

use Stru\StruHyperfAuth\Authenticatable;

class Validated
{
    /**
     * The authentication guard name.
     *
     * @var string
     */
    public $guard;

    /**
     * The user retrieved and validated from the User Provider.
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
