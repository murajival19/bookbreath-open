<?php

namespace App\Http\Controllers;

use App\Book;
use App\Post;
use App\Booklike;
use Validator;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;


class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $books = Book::orderBy('created_at', 'desc')->paginate(20);

        // likeカウントとlikedの判定（book,複数）
        $books->load('booklike');
        foreach ($books as $book) {
            $book->defaultCount = count($book->booklike);

            $likedjudge = $book->booklike->where('user_id', Auth::id())->first();
            if (!isset($likedjudge)) {
                $book->defaultLiked = false;
            } else {
                $book->defaultLiked = true;
            }
        }

        return view('books.index', ['books' => $books]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_title' => 'required|unique:books,book_title'
        ]);

        if ($validator->fails()) {
            $book = Book::where('book_title', $request->book_title)->first();
            return redirect()->route('books.show', $book)->withErrors($validator)->withInput();
        }

        // SSLの画像を取得
        $requestData = $request->all();
        $requestData['book_image_url'] = str_replace('http://', 'https://', $request->book_image_url);

        $book = new Book;
        $book->fill($requestData)->save();

        // likeの登録
        $booklike = new Booklike;
        $booklike->user_id = Auth::id();
        $booklike->book_id = $book->id;
        $booklike->save();

        // likeカウントとlikedの判定（book,単独）
        $book->load('booklike');
        $book->defaultCount = count($book->booklike);

        $likedjudge = $book->booklike->where('user_id', Auth::id())->first();
        if (!isset($likedjudge)) {
            $book->defaultLiked = false;
        } else {
            $book->defaultLiked = true;
        }

        return view('books.create',[
            'book' => $book,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        $posts = Post::where('book_id', $book->id)
        ->where('reply_id', null)
        ->orderBy('created_at', 'desc')
        ->paginate(5);

        $posts->load(
            'image',
            'user.thumbnail_image',
            'post_children',
            'post_reference.user.thumbnail_image',
            'post_reference.book',
            'post_reference.post_parent.user'
        );

        // likeカウントとlikedの判定（book,単独）
        $book->load('booklike');
        $book->defaultCount = count($book->booklike);

        $likedjudge = $book->booklike->where('user_id', Auth::id())->first();
        if (!isset($likedjudge)) {
            $book->defaultLiked = false;
        } else {
            $book->defaultLiked = true;
        }

        // likeカウントとlikedの判定（post,複数）
        $posts->load('postlike');
        foreach ($posts as $post) {
            $post->defaultCount = count($post->postlike);

            $likedjudge = $post->postlike->where('user_id', Auth::id())->first();
            if (!isset($likedjudge)) {
                $post->defaultLiked = false;
            } else {
                $post->defaultLiked = true;
            }
        }

        return view('books.show', [
            'book' => $book,
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit(Book $book)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        //
    }


    public function search(Request $request) {

        if ($request->search != "") {

            // スペースでアンド検索
            $search_temp = str_replace([" ","　"], '|', $request->search);

            if (strpos($search_temp, "|")) {
                $search_array = explode("|", $search_temp);

                $books = Book::where(function ($query) use ($search_array){
                    foreach ($search_array as $search) {
                        $query->where('book_title', 'like', "%{$search}%");
                    }
                })
                ->orWhere(function ($query) use ($search_array){
                    foreach ($search_array as $search) {
                        $query->where('author', 'like', "%{$search}%");
                    }
                })
                ->orWhere(function ($query) use ($search_array){
                    foreach ($search_array as $search) {
                        $query->where('book_description', 'like', "%{$search}%");
                    }
                })
                ->paginate(5);

            } else {

                $books = Book::where('book_title', 'like', "%{$request->search}%")
                    ->orWhere('author', 'like', "%{$request->search}%")
                    ->orWhere('book_description', 'like', "%{$request->search}%")
                    ->paginate(5);
            }

            // likeカウントとlikedの判定（book,複数）
            $books->load('booklike');
            foreach ($books as $book) {
                $book->defaultCount = count($book->booklike);

                $likedjudge = $book->booklike->where('user_id', Auth::id())->first();
                if (!isset($likedjudge)) {
                    $book->defaultLiked = false;
                } else {
                    $book->defaultLiked = true;
                }
            }

            return view('books.search',[
                'books' => $books,
                'search_query' => $request->search,
                'search_count' => $books->total(),
            ]);
        } else {

            return redirect('/');

        }

    }

    public function externalSearch(Request $request)
    {
        if (!empty($request->title) || !empty($request->author) || !empty($request->isbn) || !empty($request->all)){

            // スペースでアンド検索
            // クエリの作成
            $request_array = $request->all();
            $query = '';
            if (!empty($request_array['all'])) {
                $request_array['all'] = str_replace([" ","　"], '+', $request_array['all']);
                $query = $query . $request_array['all'] . '+';
            }
            if (!empty($request_array['title'])) {
                $request_array['title'] = str_replace([" ","　"], '+', $request_array['title']);
                $query = $query . 'intitle:' . $request_array['title'] . '+';
            }
            if (!empty($request_array['author'])) {
                $request_array['author'] = str_replace([" ","　"], '+', $request_array['author']);
                $query = $query . 'inauthor:' . $request_array['author'] . '+';
            }
            if (!empty($request_array['isbn'])) {
                $request_array['isbn'] = str_replace([" ","　"], '+', $request_array['isbn']);
                $query = $query . 'isbn:' . $request_array['isbn'] . '+';
            }
            $query = substr_replace($query, '', strlen($query)-1);
            $country = 'JP';
            $maxResults = 40; // max:40

            $param = '?q=' . $query . '&Country=' . $country . '&maxResults=' . $maxResults;
            $url = "https://www.googleapis.com/books/v1/volumes". $param;

            $request->search = str_replace("+", ' ', $request->search);

            $option = [
                CURLOPT_RETURNTRANSFER => true, //文字列として返す
                CURLOPT_TIMEOUT        => 3, // タイムアウト時間
            ];

            $ch = curl_init($url);
            curl_setopt_array($ch, $option);

            $json    = curl_exec($ch);
            $info    = curl_getinfo($ch);
            $errorNo = curl_errno($ch);

            // OK以外はエラーなので空白配列を返す
            if ($errorNo !== CURLE_OK) {
                // 詳しくエラーハンドリングしたい場合はerrorNoで確認
                // タイムアウトの場合はCURLE_OPERATION_TIMEDOUT
                return [];
            }

            // 200以外のステータスコードは失敗とみなし空配列を返す
            if ($info['http_code'] !== 200) {
                return [];
            }

            // 文字列から変換
            $json_decode = json_decode($json);

            if ($json_decode->totalItems == 0) {

                return view('books.externalSearch',[
                    'search_title' => $request->title,
                    'search_author' => $request->author,
                    'search_isbn' => $request->isbn,
                    'search_all' => $request->all,
                    'search_count' => $json_decode->totalItems,
                ]);

            } else {

                $books = $json_decode->items;

                return view('books.externalSearch',[
                    'books' => $books,
                    'search_title' => $request->title,
                    'search_author' => $request->author,
                    'search_isbn' => $request->isbn,
                    'search_all' => $request->all,
                    'search_count' => $json_decode->totalItems,
                ]);
            }

        } else {

            return redirect('/');
        }
    }

    public function library(Book $book)
    {
        $books = Book::whereHas('booklike', function($query) {
            $query->where('user_id', Auth::id());
        })
        ->with('booklike')
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        // likeカウントとlikedの判定（book,複数）
        foreach ($books as $book) {
            $book->defaultCount = count($book->booklike);

            $likedjudge = $book->booklike->where('user_id', Auth::id())->first();
            if (!isset($likedjudge)) {
                $book->defaultLiked = false;
            } else {
                $book->defaultLiked = true;
            }
        }

        return view('books.library', ['books' => $books]);
    }

    public function librarySearch(Request $request) {

        if ($request->search != "") {

            // スペースでアンド検索
            $search_temp = str_replace([" ","　"], '|', $request->search);

            if (strpos($search_temp, "|")) {
                $search_array = explode("|", $search_temp);

                $books = Book::whereHas('booklike', function($quer) {
                    $quer->where('user_id', Auth::id());
                })
                ->where(function ($query) use ($search_array){
                    foreach ($search_array as $search) {
                        $query->where('book_title', 'like', "%{$search}%");
                    }
                })
                ->orWhere(function ($query) use ($search_array){
                    foreach ($search_array as $search) {
                        $query->where('author', 'like', "%{$search}%");
                    }
                })
                ->orWhere(function ($query) use ($search_array){
                    foreach ($search_array as $search) {
                        $query->where('book_description', 'like', "%{$search}%");
                    }
                })
                ->paginate(5);

            } else {

                $search = $request->search;
                $books = Book::whereHas('booklike', function($query) {
                    $query->where('user_id', Auth::id());
                })
                ->where(function($query) use ($search) {
                    $query->where('book_title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('book_description', 'like', "%{$search}%");
                })
                ->paginate(5);
            }

            // likeカウントとlikedの判定（book,複数）
            $books->load('booklike');
            foreach ($books as $book) {
                $book->defaultCount = count($book->booklike);

                $likedjudge = $book->booklike->where('user_id', Auth::id())->first();
                if (!isset($likedjudge)) {
                    $book->defaultLiked = false;
                } else {
                    $book->defaultLiked = true;
                }
            }

            return view('books.library',[
                'books' => $books,
                'search_query' => $request->search,
                'search_count' => $books->total(),
            ]);
        } else {
            return redirect('/books/library');
        }
    }

    public function terms() {
        return view('books.terms');
    }

    public function policy() {
        return view('books.policy');
    }

    public function buyAmazon(Request $request) {

        $url = 'https://www.amazon.co.jp/s?k=' . $request->book_title;
        return redirect()->away($url);
    }

    public function buyRakuten(Request $request) {

        $url = 'https://search.rakuten.co.jp/search/mall/' . $request->book_title;
        return redirect()->away($url);
    }

    public function howToUse() {
        return view('books.howToUse');
    }
}
