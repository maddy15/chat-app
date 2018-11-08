<?php

use Illuminate\Database\Seeder;
use App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        factory(User::class)->create([
            'email' => 'madz@yahoo.com',
            'name' => 'madz'
        ]);
        factory(User::class)->create([
            'email' => 'fifi@yahoo.com',
            'name' => 'fifi'
        ]);
        factory(User::class)->create([
            'email' => 'zafeer@yahoo.com',
            'name' => 'zafeer'
        ]);
    }
}
