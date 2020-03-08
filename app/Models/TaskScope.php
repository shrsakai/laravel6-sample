<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TaskScope
 * @package App
 */
class TaskScope extends Model
{
    const PRIVATE = 1; // 非公開
    const INTERNAL = 2; // 内部公開
    const PUBLIC = 3; // 公開
}
