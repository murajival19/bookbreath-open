<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('posts')->insert([
            [
                'user_id' => 1,
                'book_id' => 1,
                'content' => '川端康成作品の中で最高傑作です。ぜひ読んでみてください。',
            ],
            [
                'user_id' => 1,
                'book_id' => 1,
                'content' => '伊豆の踊子も好きですが、やはり川端康成作品では一番です。',

            ],
            [
                'user_id' => 2,
                'book_id' => 2,
                'content' => '何か偉大なことを成し遂げたい。そう感じさせる作品です。',
            ],
        ]);
    }
}
