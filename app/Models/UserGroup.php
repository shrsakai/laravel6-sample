<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserGroup
 *
 * @package App
 */
class UserGroup extends Model
{
    const ADMIN = 10; // 管理者
    const USER = 20; // 一般
    const GUEST = 30; // ゲスト
}
