<?php
namespace think\lock\driver;

use think\lock\contracts\Driver;
use Symfony\Component\Lock\Store\RedisStore;

class Redis implements Driver
{
    /**
     * @var driver
     */
    protected $driver;

    protected $options;

    public function __construct($options)
    {
        if (!extension_loaded('redis')) {
            $this->warn('请安装 Redis 扩展！');
        }

        $this->options = $options;
    }

    public function getDriver()
    {
        if (!$this->driver) {
        	$this->driver = new RedisStore(\think\facade\Cache::store('redis')->handler());
        }

    	return $this->driver;
    }
}