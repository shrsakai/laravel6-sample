<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Policies\TaskPolicy;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

/**
 * Class TaskController
 * @package App\Http\Controllers\Api
 */
class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Log::debug("index.");
        // with で Eagerロード の対象を指定
        $query = Task::with([
            'assignedUser',
            'user',
            'taskStatus',
            'taskScope',
        ]);

        // Like検索(スペース区切り -> AND)
        $query->whereContainsAll('task', $request->input('task'));

        if ($request->input('status_id')) {
            $query->where('task_status_id', $request->input('scope_id'));
        }

        if ($request->input('scope_id')) {
            $query->where('task_scope_id', $request->input('scope_id'));
        }

        $query->orderByParamOrder(['scope_id'  => 'task_scope_id',
                                   'status_id' => 'task_status_id',], $request->input('sort'));
        $query->orderBy('id', 'desc');
        $tasks = $query->paginate($request->input('limit', 20));
        return new TaskCollection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TaskStoreRequest $request
     * @return \Illuminate\Http\Response
     * @see TaskPolicy::store()
     */
    public function store(TaskStoreRequest $request)
    {
        Log::debug("store.");
        if (Auth::user()->cant('create', Task::class)) {
            Log::debug("Not authorized. user_id = " . Auth::user()->id);
            throw new HttpResponseException(response()->json([
                'message' => 'Taskを追加する権限がありません',
            ], 404));
        }

        $task = new Task();
        DB::transaction(function () use ($task) {
            $storeResult = $task->fill([
                'task'             => request()->input('task'),
                'task_status_id'   => request()->input('status_id'),
                'task_scope_id'    => request()->input('scope_id'),
                'assigned_user_id' => Auth::user()->id,
                'user_id'          => Auth::user()->id,
            ])->save();
            if ($storeResult) {
                return;
            }

            // 正常終了しなかった場合 Exception を発行してロールバックする
            Log::debug("Could not create Task. input = " . json_encode(request()->input()));
            throw new HttpResponseException(response()->json([
                'message' => 'Taskを追加できませんでした',
            ], 404));
        });
        return (new TaskResource($task));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\Response
     * @see TaskPolicy::show()
     */
    public function show(Task $task)
    {
        Log::debug("show.");
        if (Auth::user()->cant('view', $task)) {
            Log::debug("Not authorized. id = {$task->id}, user_id = " . Auth::user()->id);
            throw new HttpResponseException(response()->json([
                'message' => 'Taskを表示する権限がありません',
            ], 404));
        }
        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TaskUpdateRequest $request
     * @param \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     * @see TaskPolicy::update()
     */
    public function update(TaskUpdateRequest $request, Task $task)
    {
        Log::debug("update.");
        if (Auth::user()->cant('update', $task)) {
            Log::debug("Not authorized. id = {$task->id}, user_id = " . Auth::user()->id);
            throw new HttpResponseException(response()->json([
                'message' => 'Taskを更新する権限がありません。',
            ], 404));
        }


        DB::transaction(function () use ($task) {
            $updateResult = $task->fill([
                'task'           => request()->input('task'),
                'task_status_id' => request()->input('status_id'),
                'task_scope_id'  => request()->input('scope_id'),
            ])->save();

            if ($updateResult) {
                return; // 正常に更新できた場合コミット
            }

            // 正常終了しなかった場合 Exception を発行してロールバックする
            Log::debug("Could not update Task. id = {$task->id}, input = " . json_encode(Input::all()));
            throw new HttpResponseException(response()->json([
                'message' => 'Taskを更新できませんでした',
            ], 500));
        });
        return new TaskResource($task);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\Response
     * @see TaskPolicy::delete()
     */
    public function destroy(Task $task)
    {
        Log::debug("destroy.");
        if (Auth::user()->cant('delete', $task)) {
            Log::debug("Not authorized. id = {$task->id}, user_id = " . Auth::user()->id);
            throw new HttpResponseException(response()->json([
                'result'  => false,
                'message' => ['title' => 'Taskを削除する権限がありません。', 'body' => null],
            ], 404));
        }
        $result = Task::destroy($task->id);
        if ($result) {
            return response()->json([
                'result'  => true,
                'message' => 'Taskを削除しました',
            ], 200);
        } else {
            Log::debug("Could not destroy Task. id = $task->id");
            throw new HttpResponseException(response()->json([
                'result'  => false,
                'message' => 'Taskを削除できませんでした',
            ], 500));
        }
    }
}
