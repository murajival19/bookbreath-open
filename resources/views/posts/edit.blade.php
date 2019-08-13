@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <form action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <textarea class="form-control" id="content" rows="5" name="content" placeholder="どうだった？">{{ $post->content }}</textarea>
                    @if ($errors->has('content'))
                        <p style="color:tomato">{{ $errors->first('content') }}</p>
                    @endif
                </div>
                <div class="form-group">
                    <label for="image_input">画像の追加</label>
                    <input type="file" class="form-control-file" id="image_input" name="post_image">
                    @if ($errors->has('post_image'))
                        <p style="color:tomato">{{ $errors->first('post_image') }}</p>
                    @endif
                </div>
                <p>画像の削除</p>
                @foreach ($post->image as $image)
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                        <input type="checkbox" name="image_delete[]" value="{{ $image->id }}">
                        </div>
                    </div>
                    <div>
                        <img src="{{ '/storage/image/' . $image->image_name }}" class="rounded d-block img-fluid mb-1" alt="image">
                    </div>
                </div>
                @endforeach
                <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                <button type="submin" class="btn btn-success" value="update">編集を確定する</button>
            </form>
        </div>
    </div>
</div>
@endsection
