<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests\BookRequest;
use App\Repository\BookLikeRepository;
use App\Repository\BookRepository;
use App\Repository\PostRepository;
use App\Service\GoogleBooksRequestService;
use App\Service\LikeService;
use App\Service\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * 本に関するコントローラクラス
 */
class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // すべての本を取得
        $bookRepository = new BookRepository();
        $books = $bookRepository->getBooksDesc()->paginate(20);

        // likeカウントとlikedの判定（book,複数）
        $likeService = new LikeService();
        foreach ($books as $book) {
            $likeService->BookLikedJudge($book);
        }

        return view('books.index', [
            'books' => $books,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(BookRequest $request)
    {
        // SSLの画像を取得
        $requestData = $request->all();
        $requestData['book_image_url'] = str_replace('http://', 'https://', $request->book_image_url);

        // 本情報を保存
        $bookRepository = new BookRepository();
        $book = $bookRepository->saveBook($requestData);

        // likeの登録
        $bookLikeRepository = new BookLikeRepository();
        $bookLikeRepository->saveBookLike([
            'user_id' => Auth::id(),
            'book_id' => $book->id,
        ]);

        // likeカウントとlikedの判定（book,単独）
        $book->load('booklike');
        $likeService = new LikeService();
        $likeService->bookLikedJudge($book);

        return view('books.create', [
            'book' => $book,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        // 返信ではない投稿を取得
        $postRepository = new PostRepository();
        $posts = $postRepository->getPostsNotReply($book->id)->paginate(5);

        // likeカウントとlikedの判定（book,単独）
        $likeService = new LikeService();
        $likeService->bookLikedJudge($book);

        // likeカウントとlikedの判定（post,複数）
        foreach ($posts as $post) {
            $likeService->postLikedJudge($post);
        }

        return view('books.show', [
            'book' => $book,
            'posts' => $posts,
        ]);
    }

    /**
     * 本をDBより検索します。
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        if (empty($request->search)) {
            return redirect('/');
        }

        // 指定キーワードに該当する本を取得
        $searchWords = SearchService::searchWordsOrganizer($request->search);
        $bookRepository = new BookRepository();
        $books = $bookRepository->searchWords($searchWords)->paginate(5);

        // likeカウントとlikedの判定（book,複数）
        $likeService = new LikeService();
        foreach ($books as $book) {
            $likeService->bookLikedJudge($book);
        }

        return view('books.search', [
            'books' => $books,
            'search_query' => $request->search,
            'search_count' => $books->total(),
        ]);
    }

    /**
     * 本をGoogleBooksより検索します。
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function externalSearch(Request $request)
    {
        $returnArray = [
            'search_title' => $request->title,
            'search_author' => $request->author,
            'search_isbn' => $request->isbn,
            'search_all' => $request->all,
            'search_count' => 0,
        ];

        if (empty($request->title) && empty($request->author) && empty($request->isbn) && empty($request->all)) {
            return view('books.externalSearch', $returnArray);
        }

        $googleBooksRequestService = new GoogleBooksRequestService();
        // クエリのセット
        if (!empty($request->all)) {
            $googleBooksRequestService->setAll($request->all);
        }
        if (!empty($request->title)) {
            $googleBooksRequestService->setTitle($request->title);
        }
        if (!empty($request->author)) {
            $googleBooksRequestService->setAuthor($request->author);
        }
        if (!empty($request->isbn)) {
            $googleBooksRequestService->setIsbn($request->isbn);
        }

        // GoogleBooksへリクエスト
        $response = $googleBooksRequestService->fetchGoogleBooks();

        if ($response == []) {
            return redirect()->route('books.externalSearch', $returnArray)->withErrors('本情報の取得に失敗しました')->withInput();
        } elseif ($response->totalItems == 0) {
            return view('books.externalSearch', $returnArray);
        } else {
            $books = $response->items;
            return view('books.externalSearch', array_merge($returnArray, [
                'books' => $books,
                'search_count' => $response->totalItems,
            ]));
        }
    }

    /**
     * ライブラリを表示します。
     *
     * @return \Illuminate\Http\Response
     */
    public function library()
    {
        // 自分がいいねした本を取得
        $bookRepository = new BookRepository();
        $books = $bookRepository->getBooksLiked()->paginate(20);

        // likeカウントとlikedの判定（book,複数）
        $likeService = new LikeService();
        foreach ($books as $book) {
            $likeService->bookLikedJudge($book);
        }
        return view('books.library', ['books' => $books]);
    }

    /**
     * ライブラリ内の本を検索します。
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function librarySearch(Request $request)
    {
        if (empty($request->search)) {
            return redirect('/books/library');
        }

        // 指定キーワードに該当する本を取得
        $searchWords = SearchService::searchWordsOrganizer($request->search);
        $bookRepository = new BookRepository();
        $books = $bookRepository->searchWordsWithLiked($searchWords)->paginate(5);

        // likeカウントとlikedの判定（book,複数）
        $likeService = new LikeService();
        foreach ($books as $book) {
            $likeService->bookLikedJudge($book);
        }

        return view('books.library', [
            'books' => $books,
            'search_query' => $request->search,
            'search_count' => $books->total(),
        ]);
    }

    /**
     * Amazonの購入ページを開きます。
     *
     * @param Request $request
     * @return void
     */
    public function buyAmazon(Request $request)
    {
        $url = 'https://www.amazon.co.jp/s?k=' . $request->book_title;
        return redirect()->away($url);
    }

    /**
     * 楽天の購入ページを開きます。
     *
     * @param Request $request
     * @return void
     */
    public function buyRakuten(Request $request)
    {
        $url = 'https://search.rakuten.co.jp/search/mall/' . $request->book_title;
        return redirect()->away($url);
    }
}
