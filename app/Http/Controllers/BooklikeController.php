<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\BookLikeService;

/**
 * 本のいいねに関するコントローラクラス
 */
class BooklikeController extends Controller
{
    /**
     * 本のいいねに関するサービスクラスのインスタンス
     *
     * @var \App\Service\BookLikeService
     */
    private $bookLikeService;

    /**
     * コンストラクタ
     *
     * @param BookLikeService $bookLikeService
     */
    public function __construct(BookLikeService $bookLikeService)
    {
        $this->bookLikeService = $bookLikeService;
    }

    /**
     * 指定した本にいいねをします。
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function like(Request $request)
    {
        $likeCount = $this->bookLikeService->setLike($request);
        return response()->json(['likeCount' => $likeCount]);
    }

    /**
     * 指定した本のいいねを削除します。
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function unlike(Request $request)
    {
        $likeCount = $this->bookLikeService->setUnlike($request);
        return response()->json(['likeCount' => $likeCount]);
    }
}
