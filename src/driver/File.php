<?php
namespace think\lock\driver;

use think\lock\contracts\Driver;
use Symfony\Component\Lock\Store\FlockStore;

class File implements Driver
{
    /**
     * @var driver
     */
    protected $driver = null;
    
    /**
     * @var path
     */
    protected $path;

    public function __construct($options)
    {
        $this->path = $options['path'];
    }

    public function getDriver()
    {
        if (is_null($this->driver)) {
            $this->driver = new FlockStore($this->path);
        }

        return $this->driver;
    }
}