<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class TaskPolicy
 * @package App\Policies
 */
class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the task.
     *
     * @param  User $user
     * @param  Task $task
     * @return bool
     */
    public function view(User $user, Task $task)
    {
        return true;
    }

    /**
     * Determine whether the user can create tasks.
     *
     * @param  User $user
     * @return bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the task.
     *
     * @param  User $user
     * @param  Task $task
     * @return bool
     */
    public function update(User $user, Task $task)
    {
        // 作成者または担当者
        return ($user->id === $task->assigned_user_id || $user->id === $task->user_id);
    }

    /**
     * Determine whether the user can delete the task.
     *
     * @param  User $user
     * @param  Task $task
     * @return bool
     */
    public function delete(User $user, Task $task)
    {
        // 作成者
        return ($user->id === $task->user_id);
    }
}
