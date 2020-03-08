<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * faker 記述例
     *  echo $faker->randomNumber($nbDigits = NULL). PHP_EOL; // 79907610
     *  echo $faker->randomFloat($nbMaxDecimals = NULL, $min = 0, $max = NULL). PHP_EOL; // 48.8932
     *  echo $faker->numberBetween($min = 1000, $max = 9000). PHP_EOL; // 8567
     *  echo $faker->randomElement($array = array('a', 'b', 'c')). PHP_EOL; // 'b'
     *  echo $faker->numerify($string = '<h3 class="title-h3">#'). PHP_EOL; // '609'
     *  echo $faker->lexify($string = '????'). PHP_EOL; // 'wgts'
     *  echo $faker->bothify($string = '<h3 class="title-h3">??'). PHP_EOL; // '42 jz'
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        factory(User::class)->create([
            'name'              => 'admin',
            'email'             => 'admin@test.com',
            'email_verified_at' => now(),
        ]);
        // And now let's generate a few dozen users for our app:
        factory(User::class, 50)->create([
            'email_verified_at' => now(),
        ]);
    }
}
