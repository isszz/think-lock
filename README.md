
# think-lock
thinkphp8 lock based on symfony lock

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
目前支持 `File`, `Redis`, `PDO`驱动

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
