<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TaskStatus
 * @package App
 */
class TaskStatus extends Model
{
    const DRAFT = 1; // 下書き
    const WORKING = 2; // アクティブ
    const COMPLETED = 3; // 完了
}
