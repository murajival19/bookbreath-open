@extends('layouts.app')

@section('content')

<div class="container">
    <h4 class="p-2">あなただけの図書館、ライブラリを作ろう！</h4>
    <div class="card mb-3">
        <div class="row no-gutters">
            <div class="col-md-4" style="text-align:center">
                <img src="{{ '/storage/image/howtouse_01.png' }}" class="mb-1" alt="Responsive image" style="height:300px; width:auto">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h5>Chapter.01</h5>
                    <p class="card-text">さっそく、あなたの好きな本を探してみよう。</p>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="row no-gutters">
            <div class="col-md-4" style="text-align:center">
                <img src="{{ '/storage/image/howtouse_02.png' }}" class="mb-1" alt="Responsive image" style="height:300px; width:auto">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h5>Chapter.02</h5>
                    <p class="card-text">好きな本が見つかったら、<i class="far fa-heart"></i>を押してライブラリに登録しよう。</p>
                    <p class="card-text">見つからなかったら、webでさらに検索してみよう！</p>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="row no-gutters">
            <div class="col-md-4" style="text-align:center">
                <img src="{{ '/storage/image/howtouse_03.png' }}" class="mb-1" alt="Responsive image" style="height:300px; width:auto">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h5>Chapter.03</h5>
                    <p class="card-text">好きな本が見つかったら、早速ライブラリに登録しよう。</p>
                    <p class="card-text">※最大40件までしか表示できません。好きな本が見つからない場合は、キーワードを絞り混んだり、著者検索などを試してみてください。</p>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="row no-gutters">
            <div class="col-md-4" style="text-align:center">
                <img src="{{ '/storage/image/howtouse_04.png' }}" class="mb-1" alt="Responsive image" style="height:300px; width:auto">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                        <h5>Chapter.04</h5>
                    <p class="card-text">ナビバーの「ライブラリ」からあなた専用のライブラリへ移動できるよ。</p>
                    <p class="card-text">気に入っている本のリストにするも良し、これから読みたい本のリストにするも良し、家族や友達に勧めたい本のリストにするも良し！</p>
                    <p class="card-text">ライブラリの使い方はあなた次第！</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container mt-5">
    <h4 class="p-2">ブレスを投稿して、他のユーザーと情報交換しよう！</h4>
    <div class="card mb-3">
        <div class="row no-gutters">
            <div class="col-md-4" style="text-align:center">
                <img src="{{ '/storage/image/howtouse_05.png' }}" class="mb-1" alt="Responsive image" style="height:300px; width:auto">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h5>Chapter.01</h5>
                    <p class="card-text"><strong>bookbrethでは、コメントを投稿することを「ブレス」と言うよ。</strong></p>
                    <p class="card-text">本をクリックすると、画像のような本の詳細ページへ行けるよ。</p>
                    <p class="card-text">「ブレスを投稿する」をクリックして、この本の感想や読書の進捗、感動した部分や学んだ事など、好きな内容を自由に投稿して、みんなにシェアしよう！</p>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="row no-gutters">
            <div class="col-md-4" style="text-align:center">
                <img src="{{ '/storage/image/howtouse_06.png' }}" class="mb-1" alt="Responsive image" style="height:300px; width:auto">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h5>Chapter.02</h5>
                    <p class="card-text">本に投稿されたブレスは、その本の詳細ページの下に表示されるよ。</p>
                    <p class="card-text">投稿されたブレスの下にあるアイコンをクリックすることで、そのブレスにリアクションすることができるよ！</p>
                    <p class="card-text"><i class="fas fa-info-circle"></i>：そのブレスの詳細ページへ移動<br>
                    <i class="fas fa-reply"></i>：そのブレスに返信する<br>
                    <i class="fas fa-retweet"></i>：そのブレスを参照して、新たにブレスを投稿する（リブレス）<br>
                    <i class="fas fa-edit"></i>：そのブレスを編集する（投稿者のみ）<br>
                    <i class="fas fa-trash-alt"></i>：そのブレスを削除する（投稿者のみ）<br>
                    <i class="fab fa-twitter"></i>：そのブレスの内容をtwitterに投稿する</p>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="row no-gutters">
            <div class="col-md-4" style="text-align:center">
                <img src="{{ '/storage/image/howtouse_07.png' }}" class="mb-1" alt="Responsive image" style="height:300px; width:auto">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h5>Chapter.03</h5>
                    <p class="card-text">本の詳細ページではブレスの投稿の他に、<i class="far fa-heart"></i>を押して自分のライブラリに登録したり、<br>
                    「購入する」をクリックして、Amazonや楽天で本を購入することもできるよ！</p>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="row no-gutters">
            <div class="col-md-4" style="text-align:center">
                <img src="{{ '/storage/image/howtouse_08.png' }}" class="mb-1" alt="Responsive image" style="height:300px; width:auto">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h5>Chapter.04</h5>
                    <p class="card-text">ナビバーの「ブレス」をクリックすれば、みんなのブレスを確認したり、ブレスの検索ができるよ！</p>
                    <p class="card-text">また、このページからもブレスの投稿ができるので、気軽にみんなと交流してみよう。</p>
                </div>
            </div>
        </div>
    </div>
    <h3 class="pt-3" style="text-align:center">bookbrethを使って、読書ライフをより上質にしよう！</h3>
</div>
@endsection