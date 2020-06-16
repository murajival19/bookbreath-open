<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\PostLikeService;

/**
 * 投稿のいいねに関するコントローラクラス
 */
class PostlikeController extends Controller
{
    /**
     * 投稿のいいねに関するサービスクラスのインスタンス
     *
     * @var \App\Service\PostLikeService
     */
    private $postLikeService;

    /**
     * コンストラクタ
     *
     * @param PostLikeService $postLikeService
     */
    public function __construct(PostLikeService $postLikeService)
    {
        $this->postLikeService = $postLikeService;
    }

    public function like(Request $request)
    {
        $likeCount = $this->postLikeService->setLike($request);
        return response()->json(['likeCount' => $likeCount]);
    }

    public function unlike(Request $request)
    {
        $likeCount = $this->postLikeService->setUnlike($request);
        return response()->json(['likeCount' => $likeCount]);
    }
}
