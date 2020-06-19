<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests\BookRequest;
use App\Service\BookService;
use Illuminate\Http\Request;

/**
 * 本に関するコントローラクラス
 */
class BookController extends Controller
{
    /**
     * 本に関するサービスクラスのインスタンス
     *
     * @var \App\Service\BookService
     */
    private $bookService;

    /**
     * コンストラクタ
     *
     * @param BookService $bookService
     */
    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $books = $this->bookService->getBooks();

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

        $book = $this->bookService->createBook($requestData);

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
        $param = $this->bookService->getBookWithPosts($book);
        return view('books.show', $param);
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

        $param = $this->bookService->getBooksSearch($request->search);
        return view('books.search', $param);
    }

    /**
     * 本をGoogleBooksより検索します。
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function externalSearch(Request $request)
    {
        $searchParam = [
            'search_title' => $request->title,
            'search_author' => $request->author,
            'search_isbn' => $request->isbn,
            'search_all' => $request->all,
            'search_count' => 0,
        ];

        if (empty($request->title) && empty($request->author) && empty($request->isbn) && empty($request->all)) {
            return view('books.externalSearch', $searchParam);
        }

        $response = $this->bookService->getBooksExternalSearch($searchParam);

        if ($response == []) {
            return redirect()->route('books.externalSearch', $searchParam)->withErrors('本情報の取得に失敗しました')->withInput();
        } elseif ($response->totalItems == 0) {
            return view('books.externalSearch', $searchParam);
        } else {
            $books = $response->items;
            return view('books.externalSearch', array_merge($searchParam, [
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
        $param = $this->bookService->getBooksWithLiked();
        return view('books.library', $param);
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
        $param = $this->bookService->getBooksSearch($request->search);
        return view('books.library', $param);
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
