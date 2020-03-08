<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_statuses', function (Blueprint $table) {
            $table->increments('id')->comment('タスクステータスID');
            $table->string('name')->comment('タスクステータス名');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE " . DB::getTablePrefix() . "task_statuses COMMENT 'タスクステータス'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_statuses');
    }
}
