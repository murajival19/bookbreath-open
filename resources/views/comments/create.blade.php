@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
                {{-- <div class="card-header">トークをかく</div> --}}
                <form action="{{ route('comments.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="comment"></label>
                        <textarea class="form-control" id="comment" rows="5" name="comment">{{ old('comment') }}</textarea>
                    </div>
                    {{-- <div class="form-group">
                        <label for="exampleFormControlFile1"></label>
                        <input type="file" class="form-control-file" id="exampleFormControlFile1" name="comment_image">
                    </div> --}}
                    <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                    <input type="hidden" name="post_id" value="{{ $post_id }}">
                    <button type="submit" class="btn btn-primary">投稿する</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
