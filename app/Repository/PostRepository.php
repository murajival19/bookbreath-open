<?php

namespace App\Repository;

use App\Post;

/**
 * 投稿に関するリポジトリクラス
 */
class PostRepository
{
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
            ->orderBy('created_at', 'desc')
            ->with(
                'image',
                'user.thumbnail_image',
                'post_children',
                'post_reference.user.thumbnail_image',
                'post_reference.book',
                'post_reference.post_parent.user',
                'postlike',
            );
    }
}
