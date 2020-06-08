<?php

namespace App\Service;

use Illuminate\Support\Facades\Auth;

/**
 * いいねに関するサービスクラス
 */
class LikeService
{
    /**
     * 本のいいね数を集計し、自分がいいねをしたアイテムを判別します
     *
     * @param App\Book $books
     * @return void
     */
    public function bookLikedJudge(\App\Book $book)
    {
        $book->defaultCount = count($book->booklike);
        $likedjudge = $book->booklike->where('user_id', Auth::id())->first();
        if (!isset($likedjudge)) {
            $book->defaultLiked = false;
        } else {
            $book->defaultLiked = true;
        }
    }

    /**
     * 投稿のいいね数を集計し、自分がいいねをしたアイテムを判別します
     *
     * @param App\Post $post
     * @return void
     */
    public function postLikedJudge(\App\Post $post)
    {
        $post->defaultCount = count($post->postlike);
        $likedjudge = $post->postlike->where('user_id', Auth::id())->first();
        if (!isset($likedjudge)) {
            $post->defaultLiked = false;
        } else {
            $post->defaultLiked = true;
        }
    }
}
