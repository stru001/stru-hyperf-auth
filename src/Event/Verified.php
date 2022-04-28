<?php

declare(strict_types=1);

namespace Stru\StruHyperfAuth\Event;

use Stru\StruHyperfAuth\Contract\MustVerifyEmail;

class Verified
{
    /**
     * The verified user.
     *
     * @var MustVerifyEmail
     */
    public $user;

    /**
     * Create a new event instance.
     */
    public function __construct(MustVerifyEmail $user)
    {
        $this->user = $user;
    }
}
