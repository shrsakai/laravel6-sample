<?php

namespace App\Models\Traits;

use App\Observers\UserstampObserver;

trait UserstampTrait
{
    /**
     * boot + trait 名で boot 時に必ず呼び出される
     */
    public static function bootUserstampTrait()
    {
        self::observe(UserstampObserver::class);
    }
}
