<?php
declare(strict_types=1);

namespace think\lock;

use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;

use think\App;

class Locker
{
	/**
	 * @var \think\App
	 */
	protected $app;

	/**
	 * @var Factory
	 */
	protected $factory;

	/**
	 * @var array
	 */
	protected $config;
	
	/**
	 * Lock constructor
	 *
	 * @param  \think\App  $app
	 */
	public function __construct(App $app)
	{
		$this->app = $app;
		$this->config = $app['config']->get('locker');
	}

	/**
	 * Create lock
	 * 
	 * @param string $key
	 * @param float|null $ttl 锁超时时间
	 * @param bool|null $autoRelease 是否自动释放锁
	 * @param string|null $prefix 锁前缀
	 * @return LockInterface
	 */
	public function lock(string $key, ?float $ttl = null, ?bool $autoRelease = null, ?string $prefix = null)
	{
		$ttl = $ttl ?: ($this->config['ttl'] ?? 300);
		$autoRelease = $autoRelease ?: ($this->config['auto_release'] ?? true);
		$prefix = $prefix ?: ($this->config['prefix'] ?? 'lock_');

		return $this->getFactory()->createLock($prefix . $key, $ttl, $autoRelease);
	}

	protected function getFactory()
	{
		if (!$this->factory) {
			$driver = $this->config['driver'];
			$options = $this->config['drivers'][$driver] ?? [];

			$instance = $this->getDriver($driver, $options);

			$this->factory = new LockFactory($instance);
		}

		return $this->factory;
	}

	/**
	 * @param string $driver
	 * @param array $options
	 * @return \Symfony\Component\Lock\StoreInterface
	 * @throws \Exception
	 */
	public function getDriver(string $driver, ?array $options = [])
	{
		$drivers = [
			'file' => \think\lock\driver\File::class,
			'redis' => \think\lock\driver\Redis::class,
			'pdo' => \think\lock\driver\pdo::class,
		];

		$class = $options['class'] ?? ($drivers[$driver] ?? null);

		if (!$class || !class_exists($class)) {
			throw new \InvalidArgumentException('Unable to determine lock driver class for '. $driver);
		}

		return $this->app->make($class, ['options' => $options])->getDriver();
	}
}
