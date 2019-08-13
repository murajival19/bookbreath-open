@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    {{-- 親コメント --}}
                    @if (isset($post->post_parent))
                        <p>返信先</p>
                        <div class="card mt-3 ml-3 mb-3 border-right-0 border-left-0 rounded-0">
                            <div class="row no-gutters">
                                <div class="col-2 my-2 pl-2">
                                    <img class="card-img rounded-circle" src="
                                    @if (isset($post->post_parent->user->thumbnail_image->image_name))
                                    {{ '/storage/image/' . $post->post_parent->user->thumbnail_image->image_name }}
                                    @else
                                    {{ '/storage/image/noimage500.png' }}
                                    @endif
                                    ">
                                </div>
                                <div class="col-10">
                                    <div class="card-body">
                                        <div class="row justify-content-between">
                                            <span>{{ $post->post_parent->user->name }} : <a href="{{ route('users.show', $post->post_parent->user) }}">{{ $post->post_parent->user->name_id }}</a></span>
                                            <span style="font-size:12px; color:#999 text-align:right;">{{ $post->post_parent->created_at }}</span>
                                        </div>
                                        <div>
                                            {{-- bookのブレスの場合 --}}
                                            @if (isset($post->post_parent->book))
                                                <span style="font-size:12px; color:#999;">
                                                    <a href="{{ route('books.show', [
                                                    'book_id' => $post->post_parent->book->id,
                                                    ]) }}">{{ $post->post_parent->book->book_title }}</a>のブレス
                                                </span>
                                            @endif
                                            {{-- 返信先がある場合 --}}
                                            @if (isset($post->post_parent->post_parent))
                                                <br><span style="font-size:12px; color:#999;">返信先：
                                                    <a href="{{ route('users.show', $post->post_parent->post_parent->user->id) }}">{{ $post->post_parent->post_parent->user->name }}</a>
                                                </span>
                                            @endif
                                        </div>
                                        <p>{{ mb_strimwidth($post->post_parent->content, 0, 280, '...') }}</p>
                                        @foreach ($post->post_parent->image as $image)
                                            <div>
                                                <img src="{{ '/storage/image/' . $image->image_name }}" class="img-fluid mb-1" alt="Responsive image">
                                            </div>
                                        @endforeach
                                        <postlike
                                        v-bind:post-id="{{ json_encode($post->post_parent->id) }}"
                                        v-bind:user-id="{{ json_encode(Auth::id()) }}"
                                        v-bind:default-liked="{{ json_encode($post->post_parent->defaultLiked) }}"
                                        v-bind:default-count="{{ json_encode($post->post_parent->defaultCount) }}"
                                        ></postlike>
                                        {{-- 返信数のカウント --}}
                                        @if (count($post->post_parent->post_children) != 0)
                                            <br><span style="font-size:12px; color:#999;">
                                                <a href="{{ route('posts.show', $post->post_parent) }}">{{ count($post->post_parent->post_children) }}件</a>の返信
                                            </span>
                                        @endif
                                        {{-- 画像のカウント --}}
                                        @if (count($post->post_parent->image) != 0)
                                            <br><span style="font-size:12px; color:#999;">
                                                <a href="{{ route('posts.show', $post) }}">{{ count($post->post_parent->image) }}枚</a>のイメージ
                                            </span>
                                        @endif
                                        <div class="row mt-1">
                                            {{-- 詳細 --}}
                                            <div class="col"><a href="{{ route('posts.show', [
                                                'post' => $post->post_parent,
                                            ]) }}" class="text-secondary"><i class="fas fa-info-circle"></i></a></div>
                                            {{-- 返信 --}}
                                            <div class="col"><a href="{{ route('posts.create', [
                                                'reply_id' => $post->post_parent->id,
                                                ]) }}" class="text-secondary"><i class="fas fa-reply"></i></a></div>
                                            {{-- リブレス --}}
                                            <div class="col"><a href="{{ route('posts.create', [
                                                'reference_id' => $post->post_parent->id,
                                                ]) }}" class="text-secondary"><i class="fas fa-retweet"></i></a></div>
                                            @if (Auth::id() == $post->post_parent->user->id)
                                                {{-- 編集 --}}
                                                <div class="col"><a href="{{ route('posts.edit', [
                                                    'post' => $post->post_parent,
                                                    ]) }}" class="text-secondary"><i class="fas fa-edit"></i></a></div>
                                                {{-- 削除 --}}
                                                <div class="col"><a href="{{ route('posts.delete', [
                                                    'post' => $post->post_parent,
                                                    ]) }}" class="text-secondary btn-del"><i class="fas fa-trash-alt"></i></a></div>
                                            @endif
                                            {{-- twitter --}}
                                            @if (isset($post->post_parent->book))
                                                <div class="col"><a href="{{ route('posts.twitter', [
                                                    'post' => $post->post_parent,
                                                    'user_name' => $post->post_parent->user->name,
                                                    'book_title' => $post->post_parent->book->book_title,
                                                    'url' => Request::root(),
                                                    ]) }}" class="text-secondary"><i class="fab fa-twitter"></i></a>
                                                </div>
                                            @else
                                                <div class="col"><a href="{{ route('posts.twitter', [
                                                    'post' => $post->post_parent,
                                                    'user_name' => $post->post_parent->user->name,
                                                    'url' => Request::root(),
                                                    ]) }}" class="text-secondary"><i class="fab fa-twitter"></i></a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif (!isset($post->post_parent) && isset($post->reply_id))
                        <div class="card m-3">
                            <div class="row no-gutters">
                                <div class="col-md-12 my-auto">
                                    <h5>このブレスは削除されました</h5>
                                </div>
                            </div>
                        </div>
                    @endif
                    {{-- メインコメント --}}
                    <div class="card mb-3 border-success border-right-0 border-left-0 rounded-0">
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
                                    <p>{{ $post->content }}</p>
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
                                    <div class="container">
                                        <div class="row">
                                        @foreach ($post->image as $image)
                                            <div>
                                                <img src="{{ '/storage/image/' . $image->image_name }}" class="img-fluid mb-1" alt="Responsive image">
                                            </div>
                                        @endforeach
                                        </div>
                                    </div>
                                    <postlike
                                    v-bind:post-id="{{ json_encode($post->id) }}"
                                    v-bind:user-id="{{ json_encode(Auth::id()) }}"
                                    v-bind:default-liked="{{ json_encode($post->defaultLiked) }}"
                                    v-bind:default-count="{{ json_encode($post->defaultCount) }}"
                                    ></postlike>
                                    <div class="row mt-1">
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
                    {{-- 子コメント --}}
                    @if (count($post->post_children) == 0)
                        <h5>返信はありません</h5>
                    @else
                        <p>返信</p>
                    @endif
                    @foreach ($post->post_children as $post)
                        <div class="card ml-3 border-right-0 border-bottom-0 border-left-0 rounded-0">
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
        {{ $post_children->links() }}
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
