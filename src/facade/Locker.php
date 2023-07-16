<?php
declare (strict_types = 1);

namespace think\lock\facade;

use think\Facade;

class Locker extends Facade
{
    protected static function getFacadeClass()
    {
        return 'isszz.locker';
    }
}
