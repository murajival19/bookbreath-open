<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => '佐藤光一',
                'email' => 'sato@gmail.com',
                'password' => bcrypt('testtest')
            ],
            [
                'name' => '伊藤浩二',
                'email' => 'ito@gmail.com',
                'password' => bcrypt('testtest')
            ],
            [
                'name' => '小林剛三',
                'email' => 'kobayashi@gmail.com',
                'password' => bcrypt('testtest')
            ],
        ]);
    }
}
