@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <form action="{{ route('posts.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <textarea class="form-control" id="content" rows="5" name="content" placeholder="今どうしてる？">{{ old('content') }}</textarea>
                    @if ($errors->has('content'))
                        <p style="color:tomato">{{ $errors->first('content') }}</p>
                    @endif
                </div>
                <div class="form-group mt-n3">
                    <label for="exampleFormControlFile1"></label>
                    <input type="file" class="form-control-file" id="exampleFormControlFile1" name="post_image">
                    @if ($errors->has('post_image'))
                        <p style="color:tomato">{{ $errors->first('post_image') }}</p>
                    @endif
                </div>
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                @if (isset($book_id))
                    <input type="hidden" name="book_id" value="{{ $book_id }}">
                @endif
                @if (isset($reply_id))
                    <input type="hidden" name="reply_id" value="{{ $reply_id }}">
                @endif
                @if (isset($reference_id))
                    <input type="hidden" name="reference_id" value="{{ $reference_id }}">
                @endif
                <button type="submit" class="btn btn-outline-info">投稿する</button>
            </form>
        </div>
    </div>
</div>
@endsection
