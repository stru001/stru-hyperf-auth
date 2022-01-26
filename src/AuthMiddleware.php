<?php

declare(strict_types=1);
/**
 * This file is part of stru/stru-hyperf-auth.
 */
namespace Stru\StruHyperfAuth;

use Hyperf\Contract\SessionInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use \Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Hyperf\Session\Session;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Stru\StruHyperfAuth\Exception\UnauthorizedException;
use Hyperf\Di\Annotation\Inject;

/**
 * Class AuthMiddleware.
 */
class AuthMiddleware implements MiddlewareInterface
{
    protected $guards = [null];
    /**
     * @var RequestInterface
     */

    protected $request;

    /**
     * @var HttpResponse
     */
    protected $response;

    /**
     * @Inject()
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var AuthManager
     */
    protected $auth;

    public function __construct(AuthManager $auth,RequestInterface $request,HttpResponse $response)
    {
        $this->auth = $auth;
        $this->request = $request;
        $this->response = $response;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->authenticate($request);

        return $handler->handle($request);
    }
    // 进行验证处理
    public function authenticate($request)
    {
        foreach ($this->guards as $guard) {
            if ($this->auth->guard($guard)->check()){
                return $this->auth->shouldUse($guard);
            }
        }

        $this->unauthenticated($request);
    }

    protected function unauthenticated()
    {
        $beforeUrl = $this->request->fullUrl();

        $path = $this->session->put('url:auth_before',$beforeUrl);

        throw new UnauthorizedException(
            'Unauthenticated.', null, $beforeUrl
        );
    }
}
