<?php

namespace App\Observers;

use App\Models\Task;

class TaskObserver
{
    /**
     * Handle to the task "creating" event.
     *
     * @param  \App\Models\Task $task
     * @return void
     */
    public function creating(Task $task)
    {
        $task->task = $task->task . ' Add message by creating';
    }

    /**
     * Handle the task "updating" event.
     *
     * @param  \App\Models\Task $task
     * @return void
     */
    public function updating(Task $task)
    {
        $task->task = $task->task . ' Add message by updating';
    }
}
