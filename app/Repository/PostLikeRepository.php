<?php

namespace App\Repository;

use App\Postlike;

/**
 * 投稿のいいねに関するリポジトリクラス
 */
class PostLikeRepository
{
    /**
     * 投稿のいいね情報を取得します。
     *
     * @param int $postId
     * @return \App\PostLike
     */
    public function getPostLike(int $postId)
    {
        $postLike = new Postlike();
        return $postLike->where('post_id', $postId);
    }

    /**
     * 投稿のいいね情報を保存します。
     *
     * @param array $postLikeData
     * @return \App\PostLike
     */
    public function savePostLike(array $postLikeData)
    {
        $postLike = new Postlike();
        $postLike->fill($postLikeData)->save();
        return $postLike;
    }

    /**
     * 投稿のいいね情報を削除します。
     *
     * @param int $userId
     * @param int $postId
     * @return void
     */
    public function deletePostLike(int $userId, int $postId)
    {
        $postLike = new Postlike();
        $postLike->where('user_id', $userId)->where('post_id', $postId)->delete();
    }
}
