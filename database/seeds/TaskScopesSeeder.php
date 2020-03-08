<?php

use App\Models\TaskScope;
use Illuminate\Database\Seeder;

class TaskScopesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TaskScope::truncate();
        TaskScope::create(['name' => '非公開',]);
        TaskScope::create(['name' => '内部公開',]);
        TaskScope::create(['name' => '公開',]);
    }
}
