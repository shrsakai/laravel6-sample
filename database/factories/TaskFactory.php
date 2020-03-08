<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\Models\Task::class, function (Faker $faker) {
    return [
        'task'             => $faker->realText($maxNbChars = 50),
        'task_status_id'   => $faker->numberBetween($min = 1, $max = 3),
        'task_scope_id'    => $faker->numberBetween($min = 1, $max = 3),
        'assigned_user_id' => $faker->numberBetween($min = 1, $max = 5),
        'user_id'          => $faker->numberBetween($min = 1, $max = 5),
        'created_at'       => $faker->dateTimeBetween('-1 years', '-6 months'),
        'updated_at'       => $faker->dateTimeBetween('-5 months', 'now'),
    ];
});
