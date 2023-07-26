
# think-lock
thinkphp6+ lock based on symfony lock

<p>
    <a href="https://packagist.org/packages/isszz/think-lock"><img src="https://img.shields.io/badge/php->=8.0-8892BF.svg" alt="Minimum PHP Version"></a>
    <a href="https://packagist.org/packages/isszz/think-lock"><img src="https://img.shields.io/badge/thinkphp->=6.x-8892BF.svg" alt="Minimum Thinkphp Version"></a>
    <a href="https://packagist.org/packages/isszz/think-lock"><img src="https://poser.pugx.org/isszz/think-lock/v/stable" alt="Stable Version"></a>
    <a href="https://packagist.org/packages/isszz/think-lock"><img src="https://poser.pugx.org/isszz/think-lock/downloads" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/isszz/think-lock"><img src="https://poser.pugx.org/isszz/think-lock/license" alt="License"></a>
</p>

## 安装

```shell
composer require isszz/think-lock
```

## 说明
目前支持 `File`，`Redis`，`PDO`驱动，安装后可在`config/locker.php`配置。
建议使用`Redis`或者`PDO`驱动

## 配置
```php
<?php

return [
    'driver' => 'redis', // file|redis|pdo，建议使用redi或pdo，file不支持ttl
    'drivers' => [
        'file' => [
            'path' => runtime_path('lock'), // 锁存储路径
        ],
        // 未配置时使用tp缓存类的redis，如果是默认配置且在同一个库建议留空
        'redis' => [
            'host'       => '127.0.0.1',
            'port'       => 6379,
            'password'   => '',
            'select'     => 0,
        ],
        // 使用pdo驱动时，数据库表和字段是自动创建无需自行处理
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

            // 如果表名, 和字段需要自定义, 则在这里配置
            /*
            'table' => 'lock_keys', // 表名
            'id' => 'key_id', // 锁ID
            'token' => 'key_token', // 锁token
            'expiration' => 'key_expiration', // 锁有效期
            */
        ],
    ],
    
    'ttl' => 300, // 默认锁超时时间
    'auto_release' => true, // 是否自动释放，建议设置为 true
    'prefix' => 'think_lock_', // 锁key前缀
];

```

## 使用

```php
<?php

namespace app\index\controller;

use think\lock\facade\Locker;

class Index
{
    public function add()
    {
        $locker = Locker::lock('test', ttl: 5);

        if (!$locker->acquire()) {
            return json(['code' => 1, 'msg' => '操作太频繁，请稍后再试']);
        }

        try {
            sleep(5);
            // 具体的操作代码
            return json(['code' => 0, 'msg' => '进行了一些操作']);
        } finally {
            // 解锁
            $locker->release();
        }

        return json(['code' => 0, 'msg' => 'success']);
    }
}

```

## 更多操作参考 symfony/lock 文档

https://symfony.com/doc/current/components/lock.html
