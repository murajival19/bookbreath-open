<?php

namespace App\Service;

use App\Repository\PostLikeRepository;

/**
 * 投稿のいいねに関するサービスクラス
 */
class PostLikeService
{
    /**
     * 投稿のいいねに関するリポジトリクラスのインスタンス
     *
     * @var \App\Repository\PostLikeRepository
     */
    private $postLikeRepository;

    /**
     * コンストラクタ
     *
     * @param PostLikeRepository $postLikeRepository
     */
    public function __construct(PostLikeRepository $postLikeRepository)
    {
        $this->postLikeRepository = $postLikeRepository;
    }

    /**
     * いいねをセットし、いいね数を返します。
     *
     * @param \Illuminate\Http\Request $request
     * @return int
     */
    public function setLike(\Illuminate\Http\Request $request)
    {
        $this->postLikeRepository->savePostLike([
            'user_id' => $request->user_id,
            'post_id' => $request->post_id,
        ]);
        return count($this->postLikeRepository->getPostLike($request->post_id)->get());
    }

    /**
     * いいねを解除し、いいね数を返します。
     *
     * @param \Illuminate\Http\Request $request
     * @return int
     */
    public function setUnlike(\Illuminate\Http\Request $request)
    {
        $this->postLikeRepository->deletePostLike($request->user_id, $request->post_id);
        return count($this->postLikeRepository->getPostLike($request->post_id)->get());
    }
}
