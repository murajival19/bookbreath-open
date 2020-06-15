<?php

namespace App\Repository;

use App\Post;

/**
 * 投稿に関するリポジトリクラス
 */
class PostRepository
{
    /**
     * 降順にすべての投稿を取得します。
     *
     * @return \App\Post
     */
    public function getPostsDesc()
    {
        return Post::orderBy('created_at', 'desc');
    }

    /**
     * 降順に指定user_idの投稿を取得します。
     *
     * @param int $userId
     * @return \App\Post
     */
    public function getPostsUserId(int $userId)
    {
        return Post::where('user_id', $userId)->orderBy('created_at', 'desc');
    }

    /**
     * コンテンツに該当するキーワードがある投稿を取得します。
     *
     * @param string $searchWord
     * @return \App\Post
     */
    public function getPostsSearchContent(string $searchWord)
    {
        return Post::where('content', 'like', "%{$searchWord}%")
            ->orderBy('created_at', 'desc');
    }

    /**
     * 返信ではない投稿を取得します。
     *
     * @param int $bookId
     * @return \App\Post
     */
    public function getPostsNotReply(int $bookId)
    {
        return Post::where('book_id', $bookId)
            ->where('reply_id', null)
            ->orderBy('created_at', 'desc');
    }

    /**
     * 返信投稿を取得します。
     *
     * @param int $replyId
     * @return \App\Post
     */
    public function getReplyPost(int $replyId)
    {
        return Post::find($replyId);
    }

    /**
     * 参照投稿を取得します。
     *
     * @param int $referenceId
     * @return \App\Post
     */
    public function getReferencePost(int $referenceId)
    {
        return Post::find($referenceId);
    }

    /**
     * 投稿を保存します。
     *
     * @param array $postData
     * @return \App\Post
     */
    public function savePost(array $postData)
    {
        $post = new Post();
        $post->fill($postData)->save();
        return $post;
    }

    /**
     * 投稿を削除します。
     *
     * @param int $postId
     * @return void
     */
    public function deletePost(int $postId)
    {
        Post::find($postId)->delete();
    }
}
