<?php
declare(strict_types=1);

namespace think\lock;

class Service extends \think\Service
{
    public function boot()
    {
        $this->app->bind('isszz.locker', function() {
        	return new Locker($this->app);
        });
    }
}