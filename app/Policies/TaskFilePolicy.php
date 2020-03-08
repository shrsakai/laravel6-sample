<?php

namespace App\Policies;

use App\Models\TaskFile;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskFilePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the task file.
     *
     * @param  \App\Models\User     $user
     * @param  \App\Models\TaskFile $taskFile
     * @return mixed
     */
    public function view(User $user, TaskFile $taskFile)
    {
        return true;
    }

    /**
     * Determine whether the user can create task files.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the task file.
     *
     * @param  \App\Models\User     $user
     * @param  \App\Models\TaskFile $taskFile
     * @return mixed
     */
    public function update(User $user, TaskFile $taskFile)
    {
        // ファイルの作成者
        return ($user->id === $taskFile->user_id);
    }

    /**
     * Determine whether the user can delete the task file.
     *
     * @param  \App\Models\User     $user
     * @param  \App\Models\TaskFile $taskFile
     * @return mixed
     */
    public function delete(User $user, TaskFile $taskFile)
    {
        // ファイルの作成者
        return ($user->id === $taskFile->user_id);
    }
}
