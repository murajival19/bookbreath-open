<?php

namespace App\Repository;

use App\Book;
use Illuminate\Support\Facades\Auth;

/**
 * 本に関するリポジトリクラス
 */
class BookRepository
{
    /**
     * 降順にすべての本を取得します。
     *
     * @return \App\Book
     */
    public function getBooksDesc()
    {
        return Book::orderBy('created_at', 'desc')->with('booklike');
    }

    /**
     * 自分がいいねした本を取得します。
     *
     * @return \App\Book
     */
    public function getBooksLiked()
    {
        return Book::whereHas('booklike', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->with('booklike')
            ->orderBy('created_at', 'desc');
    }

    /**
     * 本の情報を保存します。
     *
     * @param array $bookData
     * @return \App\Book
     */
    public function saveBook(array $bookData)
    {
        $book = new Book();
        $book->fill($bookData)->save();
        return $book;
    }

    /**
     * 指定のキーワードに該当する本を取得します。
     *
     * @param array $searchWords
     * @return \App\Book
     */
    public function searchWords(array $searchWords)
    {
        return Book::where(function ($query) use ($searchWords) {
            foreach ($searchWords as $word) {
                $query->where('book_title', 'like', "%{$word}%");
            }
        })->orWhere(function ($query) use ($searchWords) {
            foreach ($searchWords as $word) {
                $query->where('author', 'like', "%{$word}%");
            }
        })->orWhere(function ($query) use ($searchWords) {
            foreach ($searchWords as $word) {
                $query->where('book_description', 'like', "%{$word}%");
            }
        })->with('booklike');
    }

    /**
     * 指定のキーワードかつ、自分がいいねした本を取得します。
     *
     * @param array $searchWords
     * @return \App\Book
     */
    public function searchWordsWithLiked(array $searchWords)
    {
        return $this->searchWords($searchWords)
            ->whereHas('booklike', function ($query) {
                $query->where('user_id', Auth::id());
            });
    }
}
