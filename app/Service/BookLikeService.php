<?php

namespace App\Service;

use App\Repository\BookLikeRepository;

/**
 * 本のいいねに関するサービスクラス
 */
class BookLikeService
{
    /**
     * 本のいいねに関するリポジトリクラスのインスタンス
     *
     * @var \App\Repository\BookLikeRepository
     */
    private $bookLikeRepository;

    /**
     * コンストラクタ
     *
     * @param BookLikeRepository $bookLikeRepository
     */
    public function __construct(BookLikeRepository $bookLikeRepository)
    {
        $this->bookLikeRepository = $bookLikeRepository;
    }

    /**
     * いいねをセットし、いいね数を返します。
     *
     * @param \Illuminate\Http\Request $request
     * @return int
     */
    public function setLike(\Illuminate\Http\Request $request)
    {
        $this->bookLikeRepository->saveBookLike([
            'user_id' => $request->user_id,
            'book_id' => $request->book_id,
        ]);
        return count($this->bookLikeRepository->getBookLike($request->book_id)->get());
    }

    /**
     * いいねを解除し、いいね数を返します。
     *
     * @param \Illuminate\Http\Request $request
     * @return int
     */
    public function setUnlike(\Illuminate\Http\Request $request)
    {
        $this->bookLikeRepository->deleteBookLike($request->user_id, $request->book_id);
        return count($this->bookLikeRepository->getBookLike($request->book_id)->get());
    }
}
