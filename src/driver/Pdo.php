<?php
namespace think\lock\driver;

use think\lock\contracts\Driver;
use Symfony\Component\Lock\Store\PdoStore;

class Pdo implements Driver
{
    /**
     * @var driver
     */
    protected $driver = null;

    /**
     * Default configuration
     */
    protected $options = [
        'database' => [
            'database' => '',
            'password' => '',
            'username' => 'root',
            'hostname' => '127.0.0.1',
            'hostport' => '3306',
            'charset' => 'utf8mb4',
            'prefix' => '',
        ],
        'table' => 'lock_keys',
        'id' => 'key_id',
        'token' => 'key_token',
        'expiration' => 'key_expiration', 
    ];

    public function __construct(?array $options = [])
    {
        if (!extension_loaded('PDO')) {
            throw new \BadFunctionCallException('Please install the PDO extension!');
        }

        if (empty($options['database']) && empty($options['database']['database']) && empty($options['database']['password'])) {
            throw new \Exception('The database and password parameters are required and must be configured');
        }

        $this->options = array_merge($this->options, (array) $options);
    }

    public function getDriver()
    {
        if (is_null($this->driver)) {
        	$database = $this->options['database'] ?? [];

        	$table = $database['prefix'] . ($this->options['table'] ?? 'lock_keys');
	        $dsn = 'mysql:host='. $database['hostname'] .':'. $database['hostport'] .';charset='. $database['charset'] .';dbname='. $database['database'];

	        try {
	            $pdo = new \PDO($dsn, $database['username'], $database['password']);
		        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	        } catch(\PDOException $e) {
	        	throw new \Exception('Failed to connect to the database:' . mb_convert_encoding($e->getMessage() ?: 'unknown', 'UTF-8', 'UTF-8,GBK,GB2312,BIG5'));
	        }

            $this->driver = new PdoStore(
                $pdo,
                [
                    // 'db_username' => $database['username'],
                    // 'db_password' => $database['password'],
                    // 定义表名, 和表字段
                    'db_table' => $table, // 表名
                    'db_id_col' => $this->options['id'] ?? null, // 锁ID
                    'db_token_col' => $this->options['token'] ?? null, // 锁token
                    'db_expiration_col' => $this->options['expiration'] ?? null, // 锁有效期
                ]
            );

            // 检查表是否存在
            try {
            	$pdo->prepare('SELECT 1 FROM '. $table)->execute();
            } catch(\PDOException $e) {
            	// 不存在表, 则创建
            	$this->driver->createTable();
            }

            $pdo = null;
        }

        return $this->driver;
    }
}
