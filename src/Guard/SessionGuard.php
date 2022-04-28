<?php

namespace Stru\StruHyperfAuth\Guard;

use Hyperf\Contract\SessionInterface;
use Stru\StruHyperfAuth\Authenticatable;
use Stru\StruHyperfAuth\Exception\AuthException;
use Stru\StruHyperfAuth\Guard;
use Stru\StruHyperfAuth\UserProvider;

class SessionGuard implements Guard
{
    use GuardHelpers,Macroable;

    /**
     * @var string
     */
    protected $name;
    /**
     * @var array
     */
    protected $config;
    /**
     * @var SessionInterface
     */
    protected $session;
    /**
     * 是否退出登录
     * @var bool
     */
    private bool $loggedOut = false;

    public function __construct(
        $name,
        $config,
        UserProvider $provider,
        SessionInterface $session
    )
    {
        $this->name = $name;
        $this->config = $config;
        $this->provider = $provider;
        $this->session = $session;
    }

    public function getName()
    {
        return 'login_' . $this->name . '_' . sha1(static::class);
    }

    public function check(): bool
    {
        try {
            return $this->user() instanceof Authenticatable;
        } catch (AuthException $exception) {
            return false;
        }
    }

    /**
     * 获取当前已经验证过的用户
     * @return mixed|Authenticatable|null
     */
    public function user()
    {
        if ($this->loggedOut){
            return null;
        }
        if (! is_null($this->user)){
            return $this->user;
        }
        return null;
    }

    /**
     * 通过ID获取当前用户 （session,user）
     * @return mixed|null
     */
    public function id()
    {
        if ($this->loggedOut){
            return null;
        }
        return $this->user()
            ? $this->user()->getAuthIdentifier()
            : $this->session->get($this->getName());
    }

    /**
     * 验证用户提供的凭证
     * @param array $credentials
     * @return bool|mixed
     */
    public function validate(array $credentials = [])
    {
        $user = $this->provider->retrieveByCredentials($credentials);
        return $this->hasValidCredentials($user,$credentials);
    }

    /**
     * 验证用户提供的凭证
     * @param array $credentials
     * @return mixed|void
     */
    public function attempt(array $credentials = [])
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        if ($this->hasValidCredentials($user,$credentials)){
            $this->login($user);
            return true;
        }
        return false;
    }

    public function checkRegister(array $params = [])
    {
        if (is_null($params)){
            return false;
        }
        $data = [
            'name' => $params['name'],
            'account' => $params['name'] . substr(time(),6),
            'email' => $params['email'],
            'password' => password_hash($params['password'],PASSWORD_DEFAULT ),
            'mobile' => '1'. substr('356789',random_int(0,5),1) . substr(time(),1),
        ];
        if($this->register($data)){
            return true;
        }
        return false;
    }

    public function register(array $userInfo)
    {
        return $this->provider->createUser($userInfo);
    }

    public function login(Authenticatable $user)
    {
        $this->updateSession($user->getAuthIdentifier());
        $this->setUser($user);
    }

    public function logout()
    {
        $this->clearUserDataFromStorage();
        $this->user = null;
        $this->loggedOut = true;
    }

    protected function clearUserDataFromStorage()
    {
        // 清除session
        $this->session->remove($this->getName());
    }

    protected function hasValidCredentials($user, array $credentials)
    {
        return ! is_null($user) && $this->provider->validateCredentials($user,$credentials);
    }

    protected function updateSession($id)
    {
        $this->session->put($this->getName(),$id);
        $this->session->migrate(true);
    }
}