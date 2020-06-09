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
     * @return \App\Book
     */
    public function getPostsDesc()
    {
        return Post::orderBy('created_at', 'desc')->with(
            'user.thumbnail_image',
            'image',
            'book',
            'post_parent.user',
            'post_children',
            'post_reference.user.thumbnail_image',
            'post_reference.book',
            'post_reference.post_parent.user',
            'postlike',
        );
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
     * Postモデルオブジェクトにリレーション追加します。
     *
     * @param \App\Post|Illuminate\Pagination\LengthAwarePaginator $post
     * @return void
     */
    public function relationLoad($post)
    {
        $post->load(
            'postlike',
            'image',
            'book',
            'user.thumbnail_image',
            'post_children.post_parent.user',
            'post_children.post_children',
            'post_children.user.thumbnail_image',
            'post_children.image',
            'post_children.book',
            'post_children.postlike',
            'post_parent.post_parent.user',
            'post_parent.post_children',
            'post_parent.user.thumbnail_image',
            'post_parent.image',
            'post_parent.postlike',
            'post_parent.book',
            'post_reference.user.thumbnail_image',
            'post_reference.book',
            'post_reference.post_parent.user',
        );
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
