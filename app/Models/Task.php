<?php

namespace App\Models;

use App\Models\Traits\ApiSearchTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Task
 * @package App\Models
 * @property int                        $id TaskID
 * @property string                     $task Task
 * @property int                        $task_status_id TaskステータスID
 * @property int                        $task_scope_id Task公開範囲ID
 * @property int                        $assigned_user_id 担当者ID
 * @property int                        $user_id ユーザID
 * @property Carbon|null                $created_at
 * @property Carbon|null                $updated_at
 * @property-read User                  $assignedUser
 * @property-read TaskScope             $taskScope
 * @property-read TaskStatus            $taskStatus
 * @property-read User                  $user
 */
class Task extends Model
{
    use ApiSearchTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'task',
        'task_status_id',
        'task_scope_id',
        'assigned_user_id',
        'user_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taskScope()
    {
        return $this->belongsTo(TaskScope::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taskStatus()
    {
        return $this->belongsTo(TaskStatus::class);
    }
}
