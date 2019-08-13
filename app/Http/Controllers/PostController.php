<?php

namespace App\Http\Controllers;

use App\Post;
use App\Image;
use App\Http\Requests\PostRequest;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use  Intervention\Image\ImageServiceProvider;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::orderBy('created_at', 'desc')->paginate(10);
        $posts->load(
            'user.thumbnail_image',
            'image',
            'book',
            'post_parent.user',
            'post_children',
            'post_reference.user.thumbnail_image',
            'post_reference.book',
            'post_reference.post_parent.user'
        );

        // likeカウントとlikedの判定（post,複数）
        $posts->load('postlike');
        foreach ($posts as $post) {
            $post->defaultCount = count($post->postlike);

            $likedjudge = $post->postlike->where('user_id', Auth::id())->first();
            if (!isset($likedjudge)) {
                $post->defaultLiked = false;
            } else {
                $post->defaultLiked = true;
            }
        }

        return view('posts.index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = Auth::user()->load('thumbnail_image');
        $temp = ['user' => $user];

        // replyコメントがある時
        if (isset($request->reply_id)) {
            $book_id = Post::find($request->reply_id)->book_id;
            $temp += ['reply_id' => $request->reply_id];
            $temp += ['book_id' => $book_id];
        // referrenceコメントがある時
        } elseif (isset($request->reference_id)) {
            $book_id = Post::find($request->reference_id)->book_id;
            $temp += ['reference_id' => $request->reference_id];
            $temp += ['book_id' => $book_id];
        // book_idのみの時
        } elseif (isset($request->book_id)) {
            $temp += ['book_id' => $request->book_id];
        } else {
        // 何もない時
        }
        return view('posts.create', $temp);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $post = new Post;
        DB::transaction(function () use ($post, $request) {

            $post = $post->fill($request->all());
            $post->save();

            if (isset($request->post_image)):
                // 画像をストレージに保存
                $image = new Image;
                $img = \Image::make($request->file('post_image'));

                // ここで編集
                $img->resize(500, null, function ($constraint) {
                    $constraint->aspectRatio();
                });

                $save_path = storage_path('app/public/image/');
                $filename = uniqid("post_image_") . '.' . $request->file('post_image')->guessExtension();
                $img->save($save_path . $filename);

                // 画像パスをDBに保存
                $image->user_id = $request->user_id;
                $image->post_id = $post->id;
                $image->image_name = $filename;
                $image->save();
            endif;
        });

        // replyコメントがある時
        if (isset($request->reply_id)) {
            return redirect()->route('posts.show', $request->reply_id);
        // book_idがある時
        } elseif (isset($request->book_id)) {
            return redirect()->route('books.show', $request->book_id);
        // referenceコメントがある時、その他
        } else {
            return redirect()->route('posts.index');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post, Request $request)
    {
        $post->load([
            'image',
            'book',
            'user.thumbnail_image',
            'post_children.post_parent.user',
            'post_children.post_children',
            'post_children.user.thumbnail_image',
            'post_children.image',
            'post_children.book',
            'post_parent.post_parent.user',
            'post_parent.post_children',
            'post_parent.user.thumbnail_image',
            'post_parent.image',
            'post_parent.book',
            'post_reference.user.thumbnail_image',
            'post_reference.book',
            'post_reference.post_parent.user',
        ]);

        // likeカウントとlikedの判定（post,単数）
        $post->load('postlike');
        $post->defaultCount = count($post->postlike);

        $likedjudge = $post->postlike->where('user_id', Auth::id())->first();
        if (!isset($likedjudge)) {
            $post->defaultLiked = false;
        } else {
            $post->defaultLiked = true;
        }

         // likeカウントとlikedの判定（post_parent,単数）
        if (isset($post->post_parent)) {
            $post->load('post_parent.postlike');
            $post->post_parent->defaultCount = count($post->post_parent->postlike);

            $likedjudge = $post->post_parent->postlike->where('user_id', Auth::id())->first();
            if (!isset($likedjudge)) {
                $post->post_parent->defaultLiked = false;
            } else {
                $post->post_parent->defaultLiked = true;
            }
        }

        // likeカウントとlikedの判定（post_children,複数）
        if (isset($post->post_children)) {
            $post->load('post_children.postlike');
            foreach ($post->post_children as $post_child) {
                $post_child->defaultCount = count($post_child->postlike);

                $likedjudge = $post_child->postlike->where('user_id', Auth::id())->first();
                if (!isset($likedjudge)) {
                    $post_child->defaultLiked = false;
                } else {
                    $post_child->defaultLiked = true;
                }
            }
        }

        // ページネーションの設定
        $dp = 5; // 表示ページ数
        $post->post_children = new LengthAwarePaginator(
            $post->post_children->forPage($request->page, $dp),
            count($post->post_children),
            $dp,
            $request->page,
            array('path' => $request->url())
        );

        $temp = [
            'post' => $post,
            'post_children' => $post->post_children, // ページネーション用
        ];

        return view('posts.show', $temp);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $post->load('image');
        $user = Auth::user()->load('thumbnail_image');
        return view('posts.edit', [
            'post' => $post,
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, Post $post)
    {
        if ($request->user_id != $post->user->id) {
            return redirect('posts');
        } else {
            DB::transaction(function () use ($request, $post) {
                $post->fill($request->all())->save();
                if (isset($request->image_delete)):
                    $image_array = Image::whereIn('id', $request->image_delete)->get();
                    // ストレージの画像を削除
                    foreach ($image_array as $image) {
                        $filePath = storage_path('app/public/image/') . $image->image_name;
                        if (\File::exists($filePath)) {
                            unlink($filePath);
                            Storage::delete($image);
                        }
                    }
                    // DBの画像パスを削除
                    Image::whereIn('id', $request->image_delete)->delete();
                endif;

                if (isset($request->post_image)):
                    // 画像をストレージに保存
                    $image = new Image;
                    $img = \Image::make($request->file('post_image'));

                    // ここで編集
                    $img->resize(500, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });

                    $save_path = storage_path('app/public/image/');
                    $filename = uniqid("post_image_") . '.' . $request->file('post_image')->guessExtension();
                    $img->save($save_path . $filename);

                    // 画像パスをDBに保存
                    $image->user_id = $request->user_id;
                    $image->post_id = $post->id;
                    $image->image_name = $filename;
                    $image->save();
                endif;
            });

            return redirect()->route('posts.show', $post->id);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $image_array = Image::where('post_id', $post->id)->get();
        if (isset($image_array)) {
            foreach ($image_array as $image) {
                // ストレージの画像を削除（DBは連動して自動で削除→マイグレーション時に設定）
                $filePath = storage_path('app/public/image/') . $image->image_name;
                if (\File::exists($filePath)) {
                    unlink($filePath);
                    Storage::delete($image);
                }
            }
        }
        // ポストを削除
        Post::find($post->id)->delete();

        return redirect('/posts');
    }

    public function delete(Post $post)
    {
        $image_array = Image::where('post_id', $post->id)->get();
        if (isset($image_array)) {
            foreach ($image_array as $image) {
                // ストレージの画像を削除（DBは連動して自動で削除→マイグレーション時に設定）
                $filePath = storage_path('app/public/image/') . $image->image_name;
                if (\File::exists($filePath)) {
                    unlink($filePath);
                    Storage::delete($image);
                }
            }
        }
        // ポストを削除
        Post::find($post->id)->delete();

        return redirect('/posts');
    }

    public function search(Request $request) {
        $posts = Post::where('content', 'like', "%{$request->search}%")
        ->orderBy('created_at', 'desc')
        ->paginate(10);
        $posts->load(
            'user.thumbnail_image',
            'book',
            'user',
            'image',
            'post_parent.user',
            'post_children',
            'post_reference.user.thumbnail_image',
            'post_reference.book',
            'post_reference.post_parent.user'
        );

        $search_count = $posts->count();

        // likeカウントとlikedの判定（post,複数）
        $posts->load('postlike');
        foreach ($posts as $post) {
            $post->defaultCount = count($post->postlike);

            $likedjudge = $post->postlike->where('user_id', Auth::id())->first();
            if (!isset($likedjudge)) {
                $post->defaultLiked = false;
            } else {
                $post->defaultLiked = true;
            }
        }

        return view('posts.search',[
            'posts' => $posts,
            'search_query' => $request->search,
            'search_count' => $search_count,
        ]);
    }

    public function twitter(Post $post, Request $request)
    {
        $url = 'url='.$request->url.'/posts/'.$post->id;
        $text = '&text='.$post->content.' by '.$request->user_name;
        $text = preg_replace('/(?:\n|\r|\r\n)/', '', $text );

        if (isset($request->book_title)) {
            $hashtags = '&hashtags='.$request->book_title;
            $tweetShere ='https://twitter.com/intent/tweet?'.$url.$text.$hashtags;
        } else {
            $tweetShere ='https://twitter.com/intent/tweet?'.$url.$text;
        }

        return redirect()->away($tweetShere);
    }
}
