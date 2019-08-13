@extends('layouts.app')

@section('content')
<div class="post-img">
    <div class="container top-container">
        <div class="row justify-content-center">
            <h1 class="top-title col-12 text-center">息をするように、本を読もう</h1>
            <div class="col-md-5 mx-auto text-center">
                <form class="form-inline md-form top-search" action="{{ route('posts.search') }}" method="post">
                    @csrf
                    <input class="form-control mr-sm-2" type="text" placeholder="ブレスを探す" aria-label="Search" name="search"
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
                    @if ($search_count == 0)
                        <h5 class="mb-3">検索結果は0件でした。</h5>
                    @else
                        <h5 class="mb-3">検索結果：「{{ $search_query }}」 表示件数{{ $search_count }}件</h5>
                    @endif
                    @foreach ($posts as $post)
                        <div class="card border-right-0 border-bottom-0 border-left-0 rounded-0">
                            <div class="row no-gutters">
                                <div class="col-2 my-2 pl-2">
                                    <img class="card-img rounded-circle" src="
                                    @if (isset($post->user->thumbnail_image->image_name))
                                    {{ '/storage/image/' . $post->user->thumbnail_image->image_name }}
                                    @else
                                    {{ '/storage/image/noimage500.png' }}
                                    @endif
                                    ">
                                </div>
                                <div class="col-10">
                                    <div class="card-body">
                                        <div class="row justify-content-between">
                                            <span>{{ $post->user->name }} : <a href="{{ route('users.show', $post->user) }}">{{ $post->user->name_id }}</a></span>
                                            <span style="font-size:12px; color:#999 text-align:right;">{{ $post->created_at }}</span>
                                        </div>
                                        <div>
                                            {{-- bookのブレスの場合 --}}
                                            @if (isset($post->book))
                                                <span style="font-size:12px; color:#999;">
                                                    <a href="{{ route('books.show', [
                                                    'book_id' => $post->book->id,
                                                    ]) }}">{{ $post->book->book_title }}</a>のブレス
                                                </span>
                                            @endif
                                            {{-- 返信先がある場合 --}}
                                            @if (isset($post->post_parent))
                                                <br><span style="font-size:12px; color:#999;">返信先：
                                                    <a href="{{ route('users.show', $post->post_parent->user->id) }}">{{ $post->post_parent->user->name }}</a>
                                                </span>
                                            @endif
                                        </div>
                                        <p>{{ mb_strimwidth($post->content, 0, 280, '...') }}</p>
                                        {{-- referenceがある場合 --}}
                                        @if (isset($post->post_reference))
                                        <div class="card mb-3">
                                            <div class="row">
                                                <div class="col-2 my-2 ml-2">
                                                    <img class="card-img rounded-circle" src="
                                                    @if (isset($post->post_reference->user->thumbnail_image->image_name))
                                                    {{ '/storage/image/' . $post->post_reference->user->thumbnail_image->image_name }}
                                                    @else
                                                    {{ '/storage/image/noimage500.png' }}
                                                    @endif
                                                    ">
                                                </div>
                                                <div class="card-body col-9">
                                                    <div class="row justify-content-between">
                                                        <span>{{ $post->post_reference->user->name }} : <a href="{{ route('users.show', $post->post_reference->user) }}">{{ $post->post_reference->user->name_id }}</a></span>
                                                        <span style="font-size:12px; color:#999 text-align:right;">{{ $post->post_reference->created_at }}</span>
                                                    </div>
                                                    <div>
                                                        {{-- bookのブレスの場合 --}}
                                                        @if (isset($post->post_reference->book))
                                                            <span style="font-size:12px; color:#999;">
                                                                <a href="{{ route('books.show', [
                                                                'book_id' => $post->post_reference->book->id,
                                                                ]) }}">{{ $post->post_reference->book->book_title }}</a>のブレス
                                                            </span>
                                                        @endif
                                                        {{-- 返信先がある場合 --}}
                                                        @if (isset($post->post_reference->post_parent))
                                                            <br><span style="font-size:12px; color:#999;">返信先：
                                                                <a href="{{ route('users.show', $post->post_reference->post_parent->user->id) }}">{{ $post->post_reference->post_parent->user->name }}</a>
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <p>{{ mb_strimwidth($post->post_reference->content, 0, 280, '...') }}</p>
                                                    {{-- 詳細 --}}
                                                    <div class="col">
                                                        <a href="{{ route('posts.show', $post->post_reference) }}" class="text-secondary"><i class="fas fa-info-circle"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        <postlike
                                        v-bind:post-id="{{ json_encode($post->id) }}"
                                        v-bind:user-id="{{ json_encode(Auth::id()) }}"
                                        v-bind:default-liked="{{ json_encode($post->defaultLiked) }}"
                                        v-bind:default-count="{{ json_encode($post->defaultCount) }}"
                                        ></postlike>
                                        {{-- 返信数のカウント --}}
                                        @if (count($post->post_children) != 0)
                                            <br><span style="font-size:12px; color:#999;">
                                                <a href="{{ route('posts.show', $post) }}">{{ count($post->post_children) }}件</a>の返信
                                            </span>
                                        @endif
                                        {{-- 画像のカウント --}}
                                        @if (count($post->image) != 0)
                                            <br><span style="font-size:12px; color:#999;">
                                                <a href="{{ route('posts.show', $post) }}">{{ count($post->image) }}枚</a>のイメージ
                                            </span>
                                        @endif
                                        <div class="row mt-1">
                                            {{-- 詳細 --}}
                                            <div class="col"><a href="{{ route('posts.show', $post->id) }}" class="text-secondary"><i class="fas fa-info-circle"></i></a></div>
                                            {{-- 返信 --}}
                                            <div class="col"><a href="{{ route('posts.create', [
                                                'reply_id' => $post,
                                                ]) }}" class="text-secondary"><i class="fas fa-reply"></i></a></div>
                                            {{-- リブレス --}}
                                            <div class="col"><a href="{{ route('posts.create', [
                                                'reference_id' => $post->id,
                                                ]) }}" class="text-secondary"><i class="fas fa-retweet"></i></a></div>
                                            @if (Auth::id() == $post->user->id)
                                                {{-- 編集 --}}
                                                <div class="col"><a href="{{ route('posts.edit', [
                                                    'post' => $post,
                                                    ]) }}" class="text-secondary"><i class="fas fa-edit"></i></a></div>
                                                {{-- 削除 --}}
                                                <div class="col"><a href="{{ route('posts.delete', [
                                                    'post' => $post,
                                                    ]) }}" class="text-secondary btn-del"><i class="fas fa-trash-alt"></i></a></div>
                                            @endif
                                            {{-- twitter --}}
                                            @if (isset($post->book))
                                                <div class="col"><a href="{{ route('posts.twitter', [
                                                    'post' => $post,
                                                    'user_name' => $post->user->name,
                                                    'book_title' => $post->book->book_title,
                                                    'url' => Request::root(),
                                                    ]) }}" class="text-secondary"><i class="fab fa-twitter"></i></a>
                                                </div>
                                            @else
                                                <div class="col"><a href="{{ route('posts.twitter', [
                                                    'post' => $post,
                                                    'user_name' => $post->user->name,
                                                    'url' => Request::root(),
                                                    ]) }}" class="text-secondary"><i class="fab fa-twitter"></i></a>
                                                </div>
                                            @endif
                                        </div>
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
        {{ $posts->appends(['search' => $search_query])->links() }}
    </div>
</div>
@endsection

@section('script')
    <script>
    $(function(){
        $(".btn-del").click(function(){
            if(confirm("本当に削除しますか？")){
            //そのままsubmit（削除）
            }else{
            //cancel
            return false;
            }
        });
    });
    </script>
@endsection