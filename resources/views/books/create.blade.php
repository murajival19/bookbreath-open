@extends('layouts.app')

@section('content')
<div class="container container-book-create">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    <div class="card mb-3">
                        <div class="row no-gutters">
                            <div class="col-md-4 my-auto">
                                <img class="card-img" src="{{ $book->book_image_url }}">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $book->book_title }}</h5>
                                    <p>{{ $book->book_description }}</p>
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
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <h5>「{{ $book->book_title }}」がライブラリに追加されました！</h5>
                                <p>この本について、ブレスを投稿しませんか？</p>
                                <a class="btn btn-outline-primary" href="{{ route('posts.create', [
                                    'book_id' => $book->id,
                                    ]) }}">ブレスを投稿する</a>
                                {{-- 本の購入 --}}
                                <span class="dropdown mr-1">
                                    <button type="button" class="btn dropdown-toggle btn-buy" id="dropdownMenuOffset" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,20">
                                        購入する
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuOffset">
                                        <a class="dropdown-item" href="{{ route('books.buyAmazon', ['book_title' => $book->book_title]) }}">Amazonで買う</a>
                                        <a class="dropdown-item" href="{{ route('books.buyRakuten', ['book_title' => $book->book_title]) }}">楽天で買う</a>
                                    </div>
                                </span>
                                <a class="btn btn-secontary" href="{{ route('books.index') }}">トップに戻る</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
