<?php

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            [
                'category_name' => 'サスペンス',
            ],
            [
                'category_name' => '歴史',
            ],
            [
                'category_name' => '自己啓発',
            ],
        ]);
    }
}
