@extends('layouts.app')

@section('content')
<div class="top-img">
    <div class="container top-container">
        <div class="row justify-content-center">
            <h1 class="top-title col-12 text-center">息をするように、本を読もう</h1>
            <div class="col-md-5 mx-auto text-center">
                <form class="form-inline md-form top-search" action="{{ route('books.search') }}" method="post">
                    @csrf
                    <input class="form-control mr-sm-2" type="text" placeholder="本を探す" aria-label="Search" name="search"
                    @if (isset($search_query))
                        value="{{ $search_query }}"
                    @endif
                    >
                    <button class="btn btn-outline-primary btn-rounded my-0" type="submit">Search</button>
                </form>
                @if (isset($errors))
                    <p style="color:tomato">{{ $errors->first() }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row justify-content-center mt-3">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    @if (isset($search_query))
                    <h5>検索結果：{{ $search_count }}件</h5>
                    <div class="container exform">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <span>webでもっと探す</span>
                                <form action="{{ route('books.externalSearch') }}" method="post">
                                    @csrf
                                    タイトル:<input class="form-control" type="text" placeholder="" aria-label="Search" name="title"
                                    @if (isset($search_query))
                                        value="{{ $search_query }}"
                                    @endif
                                    >
                                    著者:<input class="form-control mr-sm-2" type="text" placeholder="" name="author">
                                    ISBN:<input class="form-control mr-sm-2" type="text" placeholder="" name="isbn">
                                    全て:<input class="form-control mr-sm-2" type="text" placeholder="" name="all">
                                    <div class="my-1">
                                        <button class="btn btn-outline-primary btn-rounded my-0" type="submit">Search</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <p>あなたの好きな本はありませんか？<i class="far fa-heart"></i>を押して自分のライブラリーに追加しよう！</p>
                    @endif
                    @foreach ($books as $book)
                        <div class="card mb-3">
                            <div class="row no-gutters">
                                <div class="col-md-4 my-auto">
                                    <a href="{{ route('books.show', $book) }}">
                                        <img class="card-img" src="{{ $book->book_image_url }}">
                                    </a>
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <div class="container">
                                            <div class="row justify-content-between">
                                                <a href="{{ route('books.show', $book) }}" style="text-decoration: none; color:#212529">
                                                    <h5 class="card-title">{{ $book->book_title }}</h5>
                                                </a>
                                                <p>
                                                    @if (isset($book->author))
                                                        {{ $book->author }}
                                                    @endif
                                                    @if (isset($book->publishedDate))
                                                        <br>{{ $book->publishedDate }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <p>{{ mb_strimwidth($book->book_description, 0, 200, "...") }}</p>
                                        <booklike
                                        v-bind:book-id="{{ json_encode($book->id) }}"
                                        v-bind:user-id="{{ json_encode(Auth::id()) }}"
                                        v-bind:default-liked="{{ json_encode($book->defaultLiked) }}"
                                        v-bind:default-count="{{ json_encode($book->defaultCount) }}"
                                        class="ml-2"
                                        ></booklike>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center m-2">
        {{ $books->appends(['search' => $search_query])->links() }}
    </div>
</div>
@endsection
