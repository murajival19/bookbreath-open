<?php

namespace App\Repository;

use App\Booklike;

/**
 * 本のいいねに関するリポジトリクラス
 */
class BookLikeRepository
{
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
}
