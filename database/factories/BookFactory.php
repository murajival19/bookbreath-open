<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Book;
use Faker\Generator as Faker;

$factory->define(Book::class, function (Faker $faker) {
    return [
        'book_title' => '嫌われる勇気',
        'author' => '岸見一郎',
        'publishedDate' => '2013-12',
        'isbn_13' => 9784478025819,
        'book_description' => '本書は、フロイト、ユングと並び「心理学の三大巨頭」と称される、アルフレッド・アドラーの思想(アドラー心理学)を、「青年と哲人の対話篇」という物語形式を用いてまとめた一冊です。欧米で絶大な支持を誇るアドラー心理学は、「どうすれば人は幸せに生きることができるか」という哲学的な問いに、きわめてシンプルかつ具体的な“答え”を提示します。この世界のひとつの真理とも言うべき、アドラーの思想を知って、あなたのこれからの人生はどう変わるのか?もしくは、なにも変わらないのか...。さあ、青年と共に「扉」の先へと進みましょう―。',
        'book_image_url' => 'http://books.google.com/books/content?id=qNMHnwEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api',
    ];
});
