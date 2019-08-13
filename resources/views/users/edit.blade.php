@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mb-3">
                <div class="row no-gutters">
                    <div class="col-2 my-2 pl-2">
                        <img class="card-img rounded-circle" src="
                        @if (isset($user->thumbnail_image->image_name))
                        {{ '/storage/image/' . $user->thumbnail_image->image_name }}
                        @else
                        {{ '/storage/image/noimage500.png' }}
                        @endif
                        ">
                    </div>
                    <div class="col-md-10">
                        <div class="card-body">
                            <h5 class="card-title">{{ $user->name }}さん、<h5>
                            <p><strong style="color:tomato">変更する項目のみ変更し、更新を確定してください。<br>（変更しない項目は操作しないでください）</strong></p>
                        </div>
                    </div>
                </div>
            </div>
            <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">名前</label>
                    <input type="text" class="form-control" id="name" placeholder="名前" name="name" value="{{ $user->name }}">
                </div>
                @if ($errors->has('name'))
                    <p style="color:tomato">{{ $errors->first('name') }}</p>
                @endif
                <div class="form-group">
                    <label for="name_id">bookbreath ID</label>
                    <input type="text" class="form-control" id="name_id" placeholder="bookbreath ID" name="name_id" value="{{ $user->name_id }}">
                </div>
                @if ($errors->has('name_id'))
                    <p style="color:tomato">{{ $errors->first('name_id') }}</p>
                @endif
                <div class="form-group">
                    <label for="exampleInputEmail1">email</label>
                    <input type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="email" name="email" value="{{ $user->email }}">
                </div>
                @if ($errors->has('email'))
                    <p style="color:tomato">{{ $errors->first('email') }}</p>
                @endif
                <div class="form-group">
                    <label for="password">password</label>
                    <input type="password" class="form-control" id="password" placeholder="password" name="password">
                    <input type="password" class="form-control" id="password_confirmation" placeholder="再度入力してください" name="password_confirmation">
                </div>
                @if ($errors->has('password'))
                    <p style="color:tomato">{{ $errors->first('password') }}</p>
                @endif
                <div class="form-group">
                    <label for="exampleInputEmail1">あなたについて</label>
                    <textarea class="form-control" id="user_description" rows="5" name="user_description" placeholder="あなたについて教えてください">{{ $user->user_description }}</textarea>
                    @if ($errors->has('user_description'))
                        <p style="color:tomato">{{ $errors->first('user_description') }}</p>
                    @endif
                </div>
                <div class="form-group">
                    <label for="name">website URL</label>
                    <input type="text" class="form-control" id="user_website" placeholder="website URL" name="user_website"  value="{{ $user->user_website }}">
                </div>
                <div class="form-group">
                    <label for="name">twitter URL</label>
                    <input type="text" class="form-control" id="user_twitter" placeholder="twitter URL" name="user_twitter"  value="{{ $user->user_twitter }}">
                </div>
                <div class="form-group">
                    <label for="name">facebook URL</label>
                    <input type="text" class="form-control" id="user_facebook" placeholder="facebook URL" name="user_facebook" value="{{ $user->user_facebook }}">
                </div>
                <p>現在のサムネイル画像</p>
                <img src="
                @if (isset($user->thumbnail_image->image_name))
                {{ '/storage/image/' . $user->thumbnail_image->image_name }}
                @else
                {{ '/storage/image/noimage500.png' }}
                @endif
                " class="rounded d-block" alt="user_image">
                <div class="form-group">
                    <label for="image_input">サムネイル画像の更新</label>
                    <input type="file" class="form-control-file" id="image_input" name="user_image">
                    @if ($errors->has('user_image'))
                        <p style="color:tomato">{{ $errors->first('user_image') }}</p>
                    @endif
                </div>
                <button type="submin" class="btn btn-success btn-update" value="update">編集を確定する</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script>
    $(function(){
        $(".btn-update").click(function(){
            if(confirm("プロフィールを更新します。よろしいですか？")){
            //そのままsubmit（削除）
            }else{
            //cancel
            return false;
            }
        });
    });
    </script>
@endsection