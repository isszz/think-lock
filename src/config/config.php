<?php

return [
    'driver' => 'file', // file|redis|pdo，建议使用redis，file不支持ttl
    'drivers' => [
        'file' => [
            'path' => runtime_path('lock'),
        ],
        'redis' => [],
        'pdo' => [
            // 获取thinkphp的数据库配置
            'database' => config('database.connections.'. config('database.default', 'mysql')),
            // 自行设置数据库配置
            /*
            'database' => [
                // 必须配置的参数
                'database' => '', // 数据库
                'password' => '', // 数据库密码
                // 可选配置参数
                'username' => 'root',
                'hostname' => '127.0.0.1',
                'hostport' => '3306',
                'charset' => 'utf8mb4',
                'prefix' => '', // 表前缀
            ],
            */

            // 如果表名, 或字段需要自定义, 则在这里配置
            // 'table' => 'lock_keys', // 表名
            // 'id' => 'key_id', // 锁ID
            // 'token' => 'key_token', // 锁token
            // 'expiration' => 'key_expiration', // 锁有效期
        ],
    ],
    
    'ttl' => 300, // 默认锁超时时间
    'auto_release' => true, // 是否自动释放，建议设置为 true
    'prefix' => 'think_lock_', // 锁key前缀
];
