<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskScopesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_scopes', function (Blueprint $table) {
            $table->increments('id')->comment('タスク公開範囲ID');
            $table->string('name')->comment('タスク公開範囲名');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE " . DB::getTablePrefix() . "task_scopes COMMENT 'タスク公開範囲'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_scopes');
    }
}
