<?php


namespace Stru\StruHyperfAuth;


use Stru\StruHyperfAuth\Provider\EloquentUserProvider;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
            ],
            'commands' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                    'ignore_annotations' => [
                        'mixin',
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'auth',
                    'description' => 'auth 组件配置.',
                    'source' => __DIR__ . '/../config/auth.php',
                    'destination' => BASE_PATH . '/config/autoload/auth.php',
                ],
            ],
        ];
    }
}