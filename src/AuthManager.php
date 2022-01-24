<?php


namespace Stru\StruHyperfAuth;


use Hyperf\Contract\ConfigInterface;
use Stru\StruHyperfAuth\Exception\GuardException;
use Stru\StruHyperfAuth\Exception\UserProviderException;

class AuthManager
{
    /**
     * The registered custom provider creators.
     */
    protected $customProviderCreators = [];
    /**
     * @var string
     */
    protected string $defaultDriver = 'default';
    /**
     * @var array
     */
    protected array $guards = [];
    /**
     * @var array
     */
    protected array $providers = [];
    /**
     * @var array
     */
    protected array $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config->get('auth');
    }

    public function __call($name, $arguments)
    {
        $guard = $this->guard();

        if (method_exists($guard,$name)){
            return call_user_func_array([$guard,$name],$arguments);
        }

        throw new GuardException("Method not defined : {$name}");
    }

    public function guard(string $name = null): Guard
    {
        $name = $name ?? $this->defaultGuard();

        if (empty($this->config['guards'][$name])){
            throw new GuardException("Not support this driver:{$name}");
        }

        $config = $this->config['guards'][$name];
        $userProvider = $this->provider($config['provider'] ?? $this->defaultProvider());

        return $this->guards[$name] ?? $this->guards[$name] = make(
                $config['driver'],
                [
                    $name,
                    $config,
                    $userProvider
                ]
            );
    }

    public function provider($name = null):UserProvider
    {
        $name = $name ?? $this->defaultProvider();

        if (empty($this->config['providers'][$name])) {
            throw new UserProviderException("Does not support this provider: {$name}");
        }

        $config = $this->config['providers'][$name];

        return $this->providers[$name] ?? $this->providers[$name] = make(
                $config['driver'],
                [
                    $config['model'],
                ]
            );
    }

    public function defaultProvider()
    {
        return $this->config['default']['provider'] ?? $this->defaultDriver;
    }

    public function defaultGuard():string
    {
        return $this->config['default']['guard'] ?? $this->defaultDriver;
    }
}