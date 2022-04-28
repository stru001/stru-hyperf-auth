<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/auth.
 *
 * @link     https://github.com/hyperf-ext/auth
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/auth/blob/master/LICENSE
 */
namespace Stru\StruHyperfAuth\Event;

use Stru\StruHyperfAuth\Authenticatable;

class PasswordReset
{
    /**
     * The user.
     *
     * @var Authenticatable
     */
    public $user;

    /**
     * Create a new event instance.
     */
    public function __construct(Authenticatable $user)
    {
        $this->user = $user;
    }
}