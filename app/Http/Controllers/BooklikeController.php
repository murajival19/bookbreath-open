<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\BookLikeRepository;

/**
 * 本のいいねに関するコントローラクラス
 */
class BooklikeController extends Controller
{
    /**
     * 指定した本にいいねをします。
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function like(Request $request)
    {
        $bookLikeRepository = new BookLikeRepository();
        $bookLikeRepository->saveBookLike([
            'user_id' => $request->user_id,
            'book_id' => $request->book_id,
        ]);
        $likeCount = count($bookLikeRepository->getBookLike($request->book_id)->get());
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
        $bookLikeRepository = new BookLikeRepository();
        $bookLikeRepository->deleteBookLike($request->user_id, $request->book_id);
        $likeCount = count($bookLikeRepository->getBookLike($request->book_id)->get());
        return response()->json(['likeCount' => $likeCount]);
    }
}
