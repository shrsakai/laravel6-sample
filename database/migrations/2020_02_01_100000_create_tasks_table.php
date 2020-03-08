<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id')->comment('タスクID');
            $table->string('task')->comment('タスク');
            $table->unsignedInteger('task_status_id')->comment('タスクステータスID');
            $table->unsignedInteger('task_scope_id')->comment('タスク公開範囲ID');
            $table->unsignedInteger('assigned_user_id')->comment('担当者ID');
            $table->unsignedInteger('user_id')->comment('ユーザID');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE " . DB::getTablePrefix() . "tasks COMMENT 'タスク'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
