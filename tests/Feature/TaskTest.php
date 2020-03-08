<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\ConfigController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class TaskTest
 *
 * @package Tests\Feature
 */
class TaskTest extends TestCase
{
    // テスト終了後ロールバックする
    use DatabaseTransactions;

    const TODO_STRUCTURE = [
        'id',
        'task',
        'owner'            => [
            'id',
            'name',
        ],
        'assignee'         => [
            'id',
            'name',
        ],
        'created_user',
        'modified_user',
        'scope_id',
        'status_id',
        'created_ago',
        'created_at',
        'updated_at',
        'meta_information' => [
            'permissions' => [
                'edit',
                'delete',
            ]
        ]
    ];

    public function setUp(): void
    {
        parent::setUp();
        // userId:1 でログイン
        $user = User::first();
        $this->be($user);
    }

    /**
     * @see TaskController::index()
     */
    public function testIndex()
    {
        $task = factory(Task::class)->create();

        $response = $this->getJson("/api/tasks");
//        echo json_encode($response->json(), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . PHP_EOL;
        $response->assertSuccessful()
            ->assertJsonStructure([
                'data'  => [
                    '*' => self::TODO_STRUCTURE,
                ],
                'links' => [
                    'first',
                ],
                'meta'  => [
                    'total',
                ],
            ]);
        // data 以下に 1件以上のデータが存在する
        $this->assertGreaterThanOrEqual(1, count($response->json('data')));
        collect($response->json('data'))->each(function ($item) {
            return $this->assertTypes($item);
        });
    }

    /**
     * @see TaskController::store()
     */
    public function testStore()
    {
        $task = factory(Task::class)->make();
        $storeData = $task->toArray();
        $storeData['scope_id'] = $storeData['task_scope_id'];


        // 正常系: ステータスが下書きまたはアクティブの場合
        collect([TaskStatus::DRAFT, TaskStatus::WORKING])->each((function ($item) use ($storeData) {
            $storeData['status_id'] = $item;
            $response = $this->postJson("/api/tasks", $storeData);
            $response->assertSuccessful()
                ->assertJsonStructure([
                    'data' => self::TODO_STRUCTURE,
                ]);
            $this->assertTypes($response->json('data'));
        }));

        // 異常系: データを登録(空データの場合)
        $response = $this->postJson("/api/tasks", []);
        // echo json_encode($response->json(), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) . PHP_EOL;
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['task', 'status_id', 'scope_id',])
            ->assertJsonStructure([
                'message',
                'errors',
            ]);

        // 異常系: ステータスが完了の場合
        $storeData['status_id'] = TaskStatus::COMPLETED;
        $response = $this->postJson("/api/tasks", []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status_id'])
            ->assertJsonStructure([
                'message',
                'errors',
            ]);
    }

    /**
     * @see TaskController::show()
     */
    public function testShow()
    {
        $task = factory(Task::class)->create();

        $response = $this->getJson("/api/tasks/{$task->id}");
        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => self::TODO_STRUCTURE,
            ]);
        $this->assertTypes($response->json('data'));
    }

    /**
     * @see TaskController::update()
     */
    public function testUpdate()
    {
        $admin = User::first();

        // 正常系: 作成者または担当者が本人の場合
        collect(['assigned_user_id', 'user_id'])->each(function ($item) use ($admin) {
            $task = factory(Task::class)->create([$item => $admin->id]);
            $updateData = $task->toArray();
            $updateData['scope_id'] = $updateData['task_scope_id'];
            $updateData['status_id'] = $updateData['task_status_id'];
            $response = $this->putJson("/api/tasks/{$task->id}", $updateData);
            $response->assertSuccessful()
                ->assertJsonStructure([
                    'data' => self::TODO_STRUCTURE,
                ]);
            $this->assertTypes($response->json('data'));
        });

        // 異常系: 作成者または担当者が本人ではない場合
        $user = User::find(2);
        $task = factory(Task::class)->create([
            'assigned_user_id' => $user->id,
            'user_id'          => $user->id,
        ]);
        $updateData = $task->toArray();
        $updateData['scope_id'] = $updateData['task_scope_id'];
        $updateData['status_id'] = $updateData['task_status_id'];
        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);
        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
            ]);
    }

    /**
     * @see TaskController::destroy()
     */
    public function testDestroy()
    {
        // 正常系: 作成者が本人の場合
        $admin = User::first();
        $task = factory(Task::class)->create(['user_id' => $admin->id]);
        $response = $this->deleteJson("/api/tasks/{$task->id}");
        $response->assertSuccessful()
            ->assertJsonStructure([
                'message',
            ]);

        // 異常系: 作成者が本人以外の場合
        $user = User::find(2);
        $task = factory(Task::class)->create(['user_id' => $user->id]);
        $response = $this->deleteJson("/api/tasks/{$task->id}");
        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
            ]);
    }

    /**
     * Taskデータの型チェック
     *
     * @param array $item
     * @return bool
     * @see TaskResource::toArray()
     * @see TaskFileTest::assertTypes()
     */
    private function assertTypes($item)
    {
        $this->assertIsInt($item['id']);
        $this->assertIsString($item['task']);
        $this->assertIsInt($item['owner']['id']);
        $this->assertIsString($item['owner']['name']);
        $this->assertIsInt($item['assignee']['id']);
        $this->assertIsString($item['assignee']['name']);
        $item['created_user'] && $this->assertIsArray($item['created_user']);
        $item['modified_user'] && $this->assertIsArray($item['modified_user']);
        $this->assertIsInt($item['scope_id']);
        $this->assertIsInt($item['status_id']);
        $this->assertIsString($item['created_ago']);
        $this->assertIsString($item['created_at']);
        $this->assertIsString($item['updated_at']);
        $this->assertIsString($item['meta_information']['permissions']['edit']);
        $this->assertIsString($item['meta_information']['permissions']['delete']);
        return true;
    }
}
