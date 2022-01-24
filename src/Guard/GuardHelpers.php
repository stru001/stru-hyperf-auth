<?php


namespace Stru\StruHyperfAuth\Guard;


use Stru\StruHyperfAuth\Authenticatable;
use Stru\StruHyperfAuth\Exception\AuthenticationEexception;
use Stru\StruHyperfAuth\UserProvider;

trait GuardHelpers
{
    /**
     * @var Authenticatable
     */
    protected $user;
    /**
     * @var UserProvider
     */
    protected $provider;

    public function authenticate()
    {
        if (! is_null($user = $this->user())){
            return $user;
        }

        throw new AuthenticationEexception;
    }

    public function hasUser()
    {
        return ! is_null($this->user);
    }

    public function check()
    {
        return ! is_null($this->user);
    }

    public function guest()
    {
        return ! $this->check();
    }

    public function id()
    {
        if ($this->user()){
            return $this->user()->getAuthIdentifier();
        }
    }

    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
        return $this;
    }

    public function getProvider()
    {
        return $this->provider;
    }

    public function setProvider(UserProvider $provider)
    {
        $this->provider = $provider;
    }
}