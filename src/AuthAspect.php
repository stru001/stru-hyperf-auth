<?php

declare(strict_types=1);
/**
 * This file is part of stru/stru-hyperf-auth.
 */
namespace Stru\StruHyperfAuth;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Stru\StruHyperfAuth\Annotation\Auth;
use Stru\StruHyperfAuth\Exception\UnauthorizedException;

/**
 * @Aspect
 */
class AuthAspect extends AbstractAspect
{
    public $annotations = [
        Auth::class,
    ];

    /**
     * @Inject
     * @var AuthManager
     */
    protected $auth;

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $annotation = $proceedingJoinPoint->getAnnotationMetadata();

        /** @var Auth $authAnnotation */
        $authAnnotation = $annotation->class[Auth::class] ?? $annotation->method[Auth::class];

        $guards = is_array($authAnnotation->value) ? $authAnnotation->value : [$authAnnotation->value];

        foreach ($guards as $name) {
            $guard = $this->auth->guard($name);

            if (! $guard->user() instanceof Authenticatable) {
                throw new UnauthorizedException("Without authorization from {$guard->getName()} guard", $guard);
            }
        }

        return $proceedingJoinPoint->process();
    }
}
