<?php
namespace think\lock\driver;

use think\lock\contracts\Driver;
use Symfony\Component\Lock\Store\RedisStore;

class Redis implements Driver
{
    /**
     * @var driver
     */
    protected $driver = null;

    /**
     * Redis instance
     */
    protected $handler = null;

    /**
     * Default configuration
     */
    protected $options = [
        'host'       => '127.0.0.1',
        'port'       => 6379,
        'password'   => '',
        'select'     => 0,
        'timeout'    => 0,
        'persistent' => false,
    ];

    public function __construct($options)
    {
        if (!extension_loaded('redis') && !class_exists('\Predis\Client')) {
            throw new \BadFunctionCallException('Please install the Redis extension!');
        }

        if (!is_null($this->handler)) {
            return;
        }

        if(empty($options)) {
            $this->handler = \think\facade\Cache::store('redis')->handler();
        } else {
            $this->options = array_merge($this->options, (array) $options);

            // 连接redis，来自tp缓存类的redis驱动
            if (extension_loaded('redis')) {
                $this->handler = new \Redis;

                if ($this->options['persistent']) {
                    $this->handler->pconnect($this->options['host'], (int) $this->options['port'], (int) $this->options['timeout'], 'persistent_id_' . $this->options['select']);
                } else {
                    $this->handler->connect($this->options['host'], (int) $this->options['port'], (int) $this->options['timeout']);
                }

                if ($this->options['password'] == '') {
                    $this->handler->auth($this->options['password']);
                }
            } elseif (class_exists('\Predis\Client')) {
                $params = [];
                foreach ($this->options as $key => $val) {
                    if (in_array($key, ['aggregate', 'cluster', 'connections', 'exceptions', 'profile', 'replication', 'parameters'])) {
                        $params[$key] = $val;
                        unset($this->options[$key]);
                    }
                }

                if ($this->options['password'] == '') {
                    unset($this->options['password']);
                }

                $this->handler = new \Predis\Client($this->options, $params);
            }

            if ($this->options['select'] != 0) {
                $this->handler->select((int) $this->options['select']);
            }
        }
    }

    public function getDriver()
    {
        if (is_null($this->driver)) {
        	$this->driver = new RedisStore($this->handler);
        }

    	return $this->driver;
    }
}