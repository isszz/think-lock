<?php
namespace think\lock\driver;

use think\lock\contracts\Driver;
use Symfony\Component\Lock\Store\PdoStore;

class Pdo implements Driver
{
    /**
     * @var driver
     */
    protected $driver;
    protected $options;

    public function __construct(?array $options = [])
    {
        if (!extension_loaded('PDO')) {
            $this->warn('请安装 PDO 扩展！');
        }

    	$this->options = $options;
    }

    public function getDriver()
    {
        if (!$this->driver) {

        	$options = $this->options;
        	$database = $this->options['database'] ?? [];

        	$table = ($database['prefix'] ?? '') . ($options['table'] ?? 'lock_keys');

	        $host = $database['hostname'] ?? '127.0.0.1';
	        $port = $database['hostport'] ?? '3306';
	        $dataname = $database['database'] ?? '';
	        $charset = $database['charset'] ?? 'utf8mb4';

	        $dsn = 'mysql:host='. $host .':'. $port .';charset='. $charset .';dbname='. $dataname;

	        try {
	            $pdo = new \PDO($dsn, $database['username'], $database['password']);
		        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	        } catch(\PDOException $e) {
	        	throw new \Exception('连接数据库失败：' . mb_convert_encoding($e->getMessage() ?: 'unknown', 'UTF-8', 'UTF-8,GBK,GB2312,BIG5'));
	        }

            $this->driver = new PdoStore(
                $pdo,
                [
                    // 'db_username' => $database['username'],
                    // 'db_password' => $database['password'],
                    // 如果表名, 和表字段需要自定义, 则在这里配置
                    'db_table' => $table, // 表名
                    'db_id_col' => $options['id'] ?? null, // 锁ID
                    'db_token_col' => $options['token'] ?? null, // 锁token
                    'db_expiration_col' => $options['expiration'] ?? null, // 锁有效期
                ]
            );

            // 检查表是否存在
            try {
            	$pdo->prepare('SELECT 1 FROM '. $table)->execute();
            } catch(\PDOException $e) {
            	// 不存在表, 创建
            	$this->driver->createTable();
            }

            $pdo = null;
        }

        return $this->driver;
    }
}
