<?php

namespace App\Service;

use App\Repository\BookLikeRepository;
use App\Repository\BookRepository;
use App\Repository\PostRepository;
use App\Service\LikeService;
use Illuminate\Support\Facades\Auth;

/**
 * 本に関する処理を行うサービスクラス
 */
class BookService
{
    /**
     * 本に関するリポジトリクラスのインスタンス
     *
     * @var \App\Repository\BookRepository
     */
    private $bookRepository;

    /**
     * 投稿に関するリポジトリクラスのインスタンス
     *
     * @var \App\Repository\PostRepository
     */
    private $postRepository;

    /**
     * 本のいいねにかんするリポジトリクラスのインスタンス
     *
     * @var \App\Repository\BookLikeRepository
     */
    private $bookLikeRepository;

    /**
     * いいねに関するサービスクラスのインスタンス
     *
     * @var \App\Service\LikeService
     */
    private $likeService;

    /**
     * コンストラクタ
     *
     * @param BookRepository $bookRepository
     * @param PostRepository $postRepository
     * @param BookLikeRepository $bookLikeRepository
     * @param LikeService $likeService
     */
    public function __construct(BookRepository $bookRepository, PostRepository $postRepository, BookLikeRepository $bookLikeRepository, LikeService $likeService)
    {
        $this->bookRepository = $bookRepository;
        $this->postRepository = $postRepository;
        $this->bookLikeRepository = $bookLikeRepository;
        $this->likeService = $likeService;
    }

    /**
     * すべての本を取得します。
     *
     * @return \App\Book
     */
    public function getBooks()
    {
        // すべての本を取得
        $books = $this->bookRepository->getBooksDesc()->paginate(20);

        // likeカウントとlikedの判定（book,複数）
        foreach ($books as $book) {
            $this->likeService->BookLikedJudge($book);
        }
        return $books;
    }

    /**
     * 本の情報を新規作成します。
     *
     * @param array $requestData
     * @return \App\Book
     */
    public function createBook(array $requestData)
    {
        $book = $this->bookRepository->saveBook($requestData);

        // likeの登録
        $this->bookLikeRepository->saveBookLike([
            'user_id' => Auth::id(),
            'book_id' => $book->id,
        ]);

        // likeカウントとlikedの判定（book,単独）
        $book->load('booklike');
        $this->likeService->bookLikedJudge($book);

        return $book;
    }

    /**
     * 本の詳細に表示する投稿を取得します。
     *
     * @param \App\Book $book
     * @return array
     */
    public function getBookWithPosts(\App\Book $book)
    {
        // 返信ではない投稿を取得
        $posts = $this->postRepository->getPostsNotReply($book->id)->paginate(5);

        // likeカウントとlikedの判定（book,単独）
        $this->likeService->bookLikedJudge($book);

        // likeカウントとlikedの判定（post,複数）
        foreach ($posts as $post) {
            $this->likeService->postLikedJudge($post);
        }

        return [
            'book' => $book,
            'posts' => $posts,
        ];
    }

    /**
     * 検索で本情報を取得します。
     *
     * @param string $searchWord
     * @return array
     */
    public function getBooksSearch(string $searchWord)
    {
        // 指定キーワードに該当する本を取得
        $searchWords = SearchService::searchWordsOrganizer($searchWord);
        $books = $this->bookRepository->searchWords($searchWords)->paginate(5);

        // likeカウントとlikedの判定（book,複数）
        foreach ($books as $book) {
            $this->likeService->bookLikedJudge($book);
        }

        return [
            'books' => $books,
            'search_query' => $searchWord,
            'search_count' => $books->total(),
        ];
    }

    /**
     * Google Booksの検索で本情報を取得します。
     *
     * @param array $param
     * @return mixed
     */
    public function getBooksExternalSearch(array $param)
    {
        $googleBooksRequestService = new GoogleBooksRequestService();
        // クエリのセット
        if (!empty($param['search_all'])) {
            $googleBooksRequestService->setAll($param['search_all']);
        }
        if (!empty($param['search_title'])) {
            $googleBooksRequestService->setTitle($param['search_title']);
        }
        if (!empty($param['search_author'])) {
            $googleBooksRequestService->setAuthor($param['search_author']);
        }
        if (!empty($param['search_isbn'])) {
            $googleBooksRequestService->setIsbn($param['search_isbn']);
        }

        // GoogleBooksへリクエスト
        return $googleBooksRequestService->fetchGoogleBooks();
    }

    /**
     * 自分がいいねをした本を取得します。
     *
     * @return array
     */
    public function getBooksWithLiked()
    {
        // 自分がいいねした本を取得
        $books = $this->bookRepository->getBooksLiked()->paginate(20);

        // likeカウントとlikedの判定（book,複数）
        foreach ($books as $book) {
            $this->likeService->bookLikedJudge($book);
        }

        return [
            'books' => $books,
        ];
    }
}
