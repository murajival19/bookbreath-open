<?php

namespace App\Http\Controllers;

use App\User;
use App\Image;
use App\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as ImageOperetor;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user->load('thumbnail_image');
        $posts = Post::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(10);
        $posts->load(
            'book',
            'image',
            'post_parent.user',
            'post_children',
            'user.thumbnail_image',
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

        return view('users.show', [
            'user' => $user,
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $user = User::find(Auth::id());
        $user->load('post.book');
        $param = $user->getFillable();

        // nullを""に変換
        foreach ($param as $item) {
            if (!isset($user[$item])) {
                $user[$item] = '';
            }
        }
        $user->password = '';

        return view('users.edit', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {
        DB::transaction(function () use ($request, $user) {
            $param = $user->getFillable();
            $password = $user->password;
            $thumbnail_id = $user->thumbnail_id;

            foreach ($param as $item) {
                if ($user[$item] != $request[$item]) {
                    $user[$item] = $request[$item];
                }
            }
            // パスワードの更新
            if (!empty($request->password)) {
                $user->password = Hash::make($request->password);
            } else {
                $user->password = $password;
            }
            // サムネイル画像の更新
            if (isset($request->user_image)) {
                // すでにサムネイル画像があるなら
                if (!empty($thumbnail_id)) {

                    // 今のサムネイル画像がnoimage以外なら
                    if ($user->thumbnail_id != 1) {
                        $image = Image::find($thumbnail_id);

                        // ストレージの画像を削除
                        $filePath = storage_path('app/public/image/') . $image->image_name;
                        if (File::exists($filePath)) {
                            unlink($filePath);
                            Storage::delete($image);
                        }

                        // DBの画像パスを削除
                        $image->delete();
                    }
                }

                $img = ImageOperetor::make($request->file('user_image'));

                // ここで編集
                $img->resize(200, 200);

                $save_path = storage_path('app/public/image/');
                $filename = uniqid("user_image_") . '.' . $request->file('user_image')->guessExtension();
                $img->save($save_path . $filename);

                $image = new Image();
                $image->image_name = $filename;
                $image->user_id = $user->id;
                $image->save();

                $user->thumbnail_id = $image->id;
            } else {
                $user->thumbnail_id = $thumbnail_id;
            }

            $user->save();
        });

        return redirect('/users/' . $user->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
