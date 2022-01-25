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
                [
                    'id' => 'migration',
                    'description' => 'The migration for oauth.',
                    'source' => __DIR__ . '/../database/migrations/2022_01_14_000001_create_users_table.php',
                    'destination' => BASE_PATH . '/migrations/2022_01_14_000001_create_users_table.php',
                ],
            ],
        ];
    }
}