<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Post;
use Faker\Generator as Faker;

$factory->define(Post::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'book_id' => 1,
        'content' => '川端康成作品の中で最高傑作です。ぜひ読んでみてください。',
    ];
});
