<?php

namespace App\Repository;

use App\Booklike;

/**
 * 本のいいねに関するリポジトリクラス
 */
class BookLikeRepository
{
    /**
     * 本のいいね情報を取得します。
     *
     * @param int $bookId
     * @return \App\BookLike
     */
    public function getBookLike(int $bookId)
    {
        $bookLike = new Booklike();
        return $bookLike->where('book_id', $bookId);
    }

    /**
     * 本のいいね情報を保存します。
     *
     * @param array $bookLikeData
     * @return \App\BookLike
     */
    public function saveBookLike(array $bookLikeData)
    {
        $bookLike = new Booklike();
        $bookLike->fill($bookLikeData)->save();
        return $bookLike;
    }

    /**
     * 本のいいね情報を削除します。
     *
     * @param int $userId
     * @param int $bookId
     * @return void
     */
    public function deleteBookLike(int $userId, int $bookId)
    {
        $bookLike = new Booklike();
        $bookLike->where('user_id', $userId)->where('book_id', $bookId)->delete();
    }
}
