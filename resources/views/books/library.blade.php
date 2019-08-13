@extends('layouts.app')

@section('content')
<div class="library-img">
    <div class="container top-container">
        <div class="row justify-content-center">
            <h1 class="top-title col-12 text-center">息をするように、本を読もう</h1>
            <div class="col-md-5 mx-auto text-center">
                <form class="form-inline md-form top-search" action="{{ route('books.librarySearch') }}" method="post">
                    @csrf
                    <input class="form-control mr-sm-2" type="text" placeholder="ライブラリの本を探す" aria-label="Search" name="search"
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
<div class="container topbooklist">
    <div class="row justify-content-center mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @if (isset($search_query))
                    <h5 class="mb-3">検索：「{{ $search_query }}」 表示件数{{ $search_count }}件</h5>
                    @endif
                    <div class="container">
                        <h5 class="text-center py-2 mb-3">{{ Auth::user()->name }}さんのライブラリ</h5>
                        <div class="row">
                            @foreach ($books as $book)
                                <div class="card mb-3 col-lg-3 col-md-4 col-6 border-0">
                                    <div>
                                        <a href="{{ route('books.show', $book->id) }}">
                                            <img class="card-img-top" src="{{ $book->book_image_url }}">
                                        </a>
                                    </div>
                                    <div class="card-body pr-1 pl-1">
                                        <a href="{{ route('books.show', $book->id) }}" style="text-decoration: none; color:#212529">
                                            <h5 class="card-title book-title">{{ $book->book_title }}</h5>
                                        </a>
                                        <booklike
                                        v-bind:book-id="{{ json_encode($book->id) }}"
                                        v-bind:user-id="{{ json_encode(Auth::id()) }}"
                                        v-bind:default-liked="{{ json_encode($book->defaultLiked) }}"
                                        v-bind:default-count="{{ json_encode($book->defaultCount) }}"
                                        class="ml-2"
                                        ></booklike>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center m-2">
        {{ $books->links() }}
    </div>
</div>
@endsection
