<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('books')->insert([
            [
                'book_title' => '雪国',
                'author' => '川端康成',
                'isbn_13' => 9784101001012,
                'category_id' => 1,
                'book_description' => 'ほんとうに人を好きになれるのは、もう女だけなんですから。
                雪に埋もれた温泉町で、芸者駒子と出会った島村―― ひとりの男の透徹した意識に映し出される女の美しさを、抒情豊かに描く名作。',
            ],
            [
                'book_title' => '竜馬がゆく1',
                'author' => '司馬遼太郎',
                'isbn_13' => 9784167105679,
                'category_id' => 2,
                'book_description' => '「薩長連合、大政奉還、あれァ、ぜんぶ竜馬一人がやったことさ」と、勝海舟はいった。坂本竜馬は幕末維新史上の奇蹟といわれる。かれは土佐の郷士の次男坊にすぎず、しかも浪人の身でありながらこの大動乱期に卓抜した仕事をなしえた。竜馬の劇的な生涯を中心に、同じ時代をひたむきに生きた若者たちを描く長篇小説。',
            ],
            [
                'book_title' => 'スティーブ・ジョブズ名語録',
                'author' => '桑原晃弥',
                'isbn_13' => 9784569675206,
                'category_id' => 3,
                'book_description' => 'iPod、iPhone、iPad…。数々の革新的な商品で世界を魅了しつづけるアップル社の天才CEOスティーブ・ジョブズ。彼がここまで成功できた要因は一体どこにあるのだろうか?本書は、彼が無名だった20代前半から、アップル追放の挫折をへて、現在の成功に至るまでの発言を厳選し、解説を加えた。「我慢さえできれば、うまくいったも同然なんだ」など、時代の寵児から人生のヒントを学ぶ。',

            ]
        ]);
    }
}
