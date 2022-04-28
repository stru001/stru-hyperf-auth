<?php

declare(strict_types=1);

namespace Stru\StruHyperfAuth\Guard;

use BadMethodCallException;
use Hyperf\HttpServer\Contract\RequestInterface;
use HyperfExt\Jwt\Exceptions\JwtException;
use HyperfExt\Jwt\Exceptions\UserNotDefinedException;
use HyperfExt\Jwt\Jwt;
use HyperfExt\Jwt\JwtFactory;
use HyperfExt\Jwt\Payload;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Stru\StruHyperfAuth\Authenticatable;
use Stru\StruHyperfAuth\Exception\AuthException;
use Stru\StruHyperfAuth\Guard;
use Stru\StruHyperfAuth\UserProvider;

class JwtGuard implements Guard
{
    use GuardHelpers, Macroable {
        __call as macroCall;
    }

    /**
     * The name of the Guard. Typically "jwt".
     *
     * Corresponds to guard name in authentication configuration.
     *
     * @var string
     */
    protected $name;

    /**
     * The user we last attempted to retrieve.
     *
     * @var Authenticatable
     */
    protected $lastAttempted;

    /**
     * @var \Hyperf\Contract\ContainerInterface
     */
    protected $container;

    /**
     * @var \HyperfExt\Jwt\Jwt
     */
    protected $jwt;

    /**
     * @var \Hyperf\HttpServer\Contract\RequestInterface
     */
    protected $request;

    /**
     * @var \Psr\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    protected $loggedOut = false;

    /**
     * Instantiate the class.
     */
    public function __construct(
        ContainerInterface $container,
        RequestInterface $request,
        JwtFactory $jwtFactory,
        EventDispatcherInterface $eventDispatcher,
        UserProvider $provider,
        string $name
    ) {
        $this->container = $container;
        $this->request = $request;
        $this->jwt = $jwtFactory->make();
        $this->eventDispatcher = $eventDispatcher;
        $this->provider = $provider;
        $this->name = $name;
    }

    /**
     * Magically call the JWT instance.
     *
     * @throws BadMethodCallException
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        if (method_exists($this->jwt, $method)) {
            return call_user_func_array([$this->jwt, $method], $parameters);
        }

        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        throw new BadMethodCallException("Method [{$method}] does not exist.");
    }

    public function user(): ?Authenticatable
    {
        if ($this->user !== null) {
            return $this->user;
        }

        if ($this->jwt->getToken() and
            ($payload = $this->jwt->check(true)) and
            $this->validateSubject() and
            ($this->user = $this->provider->retrieveById($payload['sub']))
        ) {
            return $this->user;
        }

        return null;
    }

    /**
     * Get the currently authenticated user or throws an exception.
     *
     * @throws \HyperfExt\Jwt\Exceptions\UserNotDefinedException
     */
    public function userOrFail(): Authenticatable
    {
        if (! $user = $this->user()) {
            throw new UserNotDefinedException();
        }

        return $user;
    }

    public function validate(array $credentials = []): bool
    {
        return (bool) $this->attempt($credentials, false);
    }

    public function attempt(array $credentials = [], bool $login = true)
    {

        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);

        if ($this->hasValidCredentials($user, $credentials)) {
            return $login ? $this->login($user) : true;
        }

        return false;
    }

    public function once(array $credentials = []): bool
    {

        if ($this->validate($credentials)) {
            $this->setUser($this->lastAttempted);

            return true;
        }

        return false;
    }

    public function login(Authenticatable $user)
    {
        $token = $this->jwt->fromUser($user);
        $this->setToken($token)->setUser($user);

        return $token;
    }

    /**
     * @param mixed $id
     * @throws \HyperfExt\Jwt\Exceptions\UserNotDefinedException
     */
    public function loginUsingId($id)
    {
        if (! is_null($user = $this->provider->retrieveById($id))) {
            return $this->login($user);
        }

        throw new UserNotDefinedException();
    }

    /**
     * @throws \HyperfExt\Jwt\Exceptions\JwtException
     */
    public function logout(bool $forceForever = false)
    {
        $user = $this->user();

        $this->requireToken()->invalidate($forceForever);

        $this->user = null;
        $this->jwt->unsetToken();
    }

    /**
     * @throws \HyperfExt\Jwt\Exceptions\JwtException
     */
    public function refresh(bool $forceForever = false)
    {
        return $this->requireToken()->refresh($forceForever);
    }

    /**
     * @throws \HyperfExt\Jwt\Exceptions\JwtException
     */
    public function invalidate(bool $forceForever = false)
    {
        return $this->requireToken()->invalidate($forceForever);
    }

    /**
     * Log the given User into the application.
     *
     * @param mixed $id
     */
    public function onceUsingId($id): bool
    {
        if ($user = $this->provider->retrieveById($id)) {
            $this->setUser($user);

            return true;
        }

        return false;
    }

    /**
     * Add any custom claims.
     *
     * @return $this
     */
    public function setCustomClaims(array $claims)
    {
        $this->jwt->setCustomClaims($claims);

        return $this;
    }

    /**
     * Get the raw Payload instance.
     *
     * @throws \HyperfExt\Jwt\Exceptions\JwtException
     */
    public function getPayload(): Payload
    {
        return $this->requireToken()->getPayload();
    }

    /**
     * Set the token.
     *
     * @param \HyperfExt\Jwt\Token|string $token
     *
     * @return $this
     */
    public function setToken($token)
    {
        $this->jwt->setToken($token);

        return $this;
    }

    public function getToken()
    {
        return $this->jwt->getToken();
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(Authenticatable $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the last user we attempted to authenticate.
     *
     * @return Authenticatable
     */
    public function getLastAttempted()
    {
        return $this->lastAttempted;
    }

    /**
     * Determine if the user matches the credentials.
     */
    protected function hasValidCredentials(?Authenticatable $user, array $credentials): bool
    {
        $validated = ($user !== null and $this->provider->validateCredentials($user, $credentials));

        return $validated;
    }

    /**
     * Ensure the JWTSubject matches what is in the token.
     *
     * @throws \HyperfExt\Jwt\Exceptions\JwtException
     */
    protected function validateSubject(): bool
    {
        // If the provider doesn't have the necessary method
        // to get the underlying model name then allow.
        if (! method_exists($this->provider, 'getModel')) {
            return true;
        }

        return $this->jwt->checkSubjectModel($this->provider->getModel());
    }

    public function check()
    {
        try {
            return $this->user() instanceof Authenticatable;
        } catch (AuthException $exception) {
            return false;
        }
    }

    public function id()
    {
        if ($this->loggedOut){
            return null;
        }
        return $this->user()
            ? $this->user()->getAuthIdentifier()
            : $this->session->get($this->getName());
    }

    public function checkRegister(array $params)
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

    /**
     * Ensure that a token is available in the request.
     *
     * @throws \HyperfExt\Jwt\Exceptions\JwtException
     */
    protected function requireToken(): Jwt
    {
        if (! $this->jwt->getToken()) {
            throw new JwtException('Token could not be parsed from the request.');
        }

        return $this->jwt;
    }
}