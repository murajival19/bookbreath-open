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
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mt-3">
                <div class="card-body">
                    @if ($search_count == 0)
                        <h5>検索結果：ヒットは0件でした</h5>
                        <p>キーワードを変更してみましょう</p>
                        <div class="container exform">
                            <div class="row justify-content-center">
                                <div class="col-md-12">
                                    <span>webでもっと探す</span>
                                    <form action="{{ route('books.externalSearch') }}" method="post">
                                        @csrf
                                        タイトル:<input class="form-control" type="text" placeholder="" aria-label="Search" name="title"
                                        @if (isset($search_title))
                                            value="{{ $search_title }}"
                                        @endif
                                        >
                                        著者:<input class="form-control mr-sm-2" type="text" placeholder="" name="author"
                                        @if (isset($search_author))
                                            value="{{ $search_author }}"
                                        @endif
                                        >
                                        ISBN:<input class="form-control mr-sm-2" type="text" placeholder="" name="isbn"
                                        @if (isset($search_isbn))
                                            value="{{ $search_isbn }}"
                                        @endif
                                        >
                                        全て:<input class="form-control mr-sm-2" type="text" placeholder="" name="all"
                                        @if (isset($search_all))
                                            value="{{ $search_all }}"
                                        @endif>
                                        <div class="my-1">
                                            <button class="btn btn-outline-primary btn-rounded my-0" type="submit">Search</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        @if ($search_count > 40)
                            <h5>検索結果：{{ $search_count }}件中40件を表示します</h5>
                        @else
                            <h5>検索結果：{{ $search_count }}件ヒットしました</h5>
                        @endif
                        <p>※精度を上げるには、さらにキーワードを絞り込んでください</p>
                        <div class="container exform">
                            <div class="row justify-content-center">
                                <div class="col-md-12">
                                    <span>webでもっと探す</span>
                                    <form action="{{ route('books.externalSearch') }}" method="post">
                                        @csrf
                                        タイトル:<input class="form-control" type="text" placeholder="" aria-label="Search" name="title"
                                        @if (isset($search_title))
                                            value="{{ $search_title }}"
                                        @endif
                                        >
                                        著者:<input class="form-control mr-sm-2" type="text" placeholder="" name="author"
                                        @if (isset($search_author))
                                            value="{{ $search_author }}"
                                        @endif
                                        >
                                        ISBN:<input class="form-control mr-sm-2" type="text" placeholder="" name="isbn"
                                        @if (isset($search_isbn))
                                            value="{{ $search_isbn }}"
                                        @endif
                                        >
                                        全て:<input class="form-control mr-sm-2" type="text" placeholder="" name="all"
                                        @if (isset($search_all))
                                            value="{{ $search_all }}"
                                        @endif>
                                        <div class="my-1">
                                            <button class="btn btn-outline-primary btn-rounded my-0" type="submit">Search</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <p>あなたの好きな本はありませんか？あなただけのライブラリに追加しよう！</p>
                        @foreach ($books as $book)
                            <div class="card mb-3">
                                <div class="row no-gutters">
                                    <div class="col-md-4 my-auto">
                                        <img class="card-img" src="
                                        @if (isset($book->volumeInfo->imageLinks->thumbnail))
                                        {{ str_replace('http://', 'https://', $book->volumeInfo->imageLinks->thumbnail) }}
                                        @else
                                        {{ '/storage/image/noimage500.png' }}
                                        @endif
                                        ">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <div class="container">
                                                <div class="row justify-content-between">
                                                    <h5 class="card-title">
                                                        @if (isset($book->volumeInfo->title))
                                                            {{ $book->volumeInfo->title }}
                                                        @endif
                                                    </h5>
                                                    <p>
                                                        @if (isset($book->volumeInfo->authors[0]))
                                                            {{ $book->volumeInfo->authors[0] }}
                                                        @endif
                                                        @if (isset($book->volumeInfo->publishedDate))
                                                            <br>{{ $book->volumeInfo->publishedDate }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <p>
                                                @if (isset($book->volumeInfo->description))
                                                    {{ $book->volumeInfo->description }}
                                                @endif
                                            </p>
                                            <form action="{{ route('books.create') }}" method="post">
                                                @csrf
                                                @if (isset($book->volumeInfo->title))
                                                    <input type="hidden" name="book_title" value="{{ $book->volumeInfo->title }}">
                                                @endif
                                                @if (isset($book->volumeInfo->authors[0]))
                                                    <input type="hidden" name="author" value="{{ $book->volumeInfo->authors[0] }}">
                                                @endif
                                                @if (isset($book->volumeInfo->publishedDate))
                                                    <input type="hidden" name="publishedDate" value="{{ $book->volumeInfo->publishedDate }}">
                                                @endif
                                                @if (isset($book->volumeInfo->industryIdentifiers[1]->identifier))
                                                    <input type="hidden" name="isbn_13" value="{{ $book->volumeInfo->industryIdentifiers[0]->identifier }}">
                                                @endif
                                                @if (isset($book->volumeInfo->description))
                                                    <input type="hidden" name="book_description" value="{{ $book->volumeInfo->description }}">
                                                @endif
                                                @if (isset($book->volumeInfo->imageLinks->thumbnail))
                                                    <input type="hidden" name="book_image_url" value="{{ $book->volumeInfo->imageLinks->thumbnail }}">
                                                @endif
                                                <button type="submit" class="btn btn-outline-danger">ライブラリに追加</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
