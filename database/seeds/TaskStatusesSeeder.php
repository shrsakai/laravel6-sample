<?php

use App\Models\TaskStatus;
use Illuminate\Database\Seeder;

class TaskStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TaskStatus::truncate();
        TaskStatus::create(['name' => '下書き',]);
        TaskStatus::create(['name' => 'アクティブ',]);
        TaskStatus::create(['name' => '完了',]);
    }
}
