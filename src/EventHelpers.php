<?php

declare(strict_types=1);

namespace Stru\StruHyperfAuth;

use Stru\StruHyperfAuth\Event\Attempting;
use Stru\StruHyperfAuth\Event\Authenticated;
use Stru\StruHyperfAuth\Event\CurrentDeviceLogout;
use Stru\StruHyperfAuth\Event\Failed;
use Stru\StruHyperfAuth\Event\Login;
use Stru\StruHyperfAuth\Event\Logout;
use Stru\StruHyperfAuth\Event\OtherDeviceLogout;
use Stru\StruHyperfAuth\Event\Validated;

trait EventHelpers
{
    /**
     * Fire the attempt event with the arguments.
     */
    protected function dispatchAttemptingEvent(array $credentials, bool $remember = false): void
    {
        $this->eventDispatcher->dispatch(new Attempting(
            $this->name,
            $credentials,
            $remember
        ));
    }

    /**
     * Fires the validated event if the dispatcher is set.
     */
    protected function dispatchValidatedEvent(Authenticatable $user)
    {
        $this->eventDispatcher->dispatch(new Validated(
            $this->name,
            $user
        ));
    }

    /**
     * Fire the login event if the dispatcher is set.
     */
    protected function dispatchLoginEvent(Authenticatable $user, bool $remember = false): void
    {
        $this->eventDispatcher->dispatch(new Login(
            $this->name,
            $user,
            $remember
        ));
    }

    /**
     * Fire the authenticated event if the dispatcher is set.
     */
    protected function dispatchAuthenticatedEvent(Authenticatable $user): void
    {
        $this->eventDispatcher->dispatch(new Authenticated(
            $this->name,
            $user
        ));
    }

    /**
     * Fire the logout event if the dispatcher is set.
     */
    protected function dispatchLogoutEvent(Authenticatable $user): void
    {
        $this->eventDispatcher->dispatch(new Logout(
            $this->name,
            $user
        ));
    }

    /**
     * Fire the current device logout event if the dispatcher is set.
     */
    protected function dispatchCurrentDeviceLogoutEvent(Authenticatable $user): void
    {
        $this->eventDispatcher->dispatch(new CurrentDeviceLogout(
            $this->name,
            $user
        ));
    }

    /**
     * Fire the other device logout event if the dispatcher is set.
     */
    protected function dispatchOtherDeviceLogoutEvent(Authenticatable $user): void
    {
        $this->eventDispatcher->dispatch(new OtherDeviceLogout(
            $this->name,
            $user
        ));
    }

    /**
     * Fire the failed authentication attempt event with the given arguments.
     */
    protected function dispatchFailedEvent(?Authenticatable $user, array $credentials): void
    {
        $this->eventDispatcher->dispatch(new Failed(
            $this->name,
            $user,
            $credentials
        ));
    }
}
