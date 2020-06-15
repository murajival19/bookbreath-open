<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Requests\UserRequest;
use App\Repository\PostRepository;
use App\Service\LikeService;
use App\Service\UserImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $postRepository = new PostRepository();
        $posts = $postRepository->getPostsUserId($user->id)->paginate(10);

        // likeカウントとlikedの判定（post,複数）
        $likeService = new LikeService();
        foreach ($posts as $post) {
            $likeService->postLikedJudge($post);
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
        $param = $user->getFillable();

        // nullを空配列に変換
        foreach ($param as $item) {
            if (!isset($user[$item])) {
                $user[$item] = '';
            }
        }
        // パスワードは空文字表示
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
            $lastPassword = $user->password;
            $lastThumbnailId = $user->thumbnail_id;

            // 各パラメータの更新
            foreach ($param as $item) {
                if ($user[$item] != $request[$item]) {
                    $user[$item] = $request[$item];
                }
            }

            // パスワードの更新
            if (!empty($request->password)) {
                $user->password = Hash::make($request->password);
            } else {
                $user->password = $lastPassword;
            }

            // サムネイル画像の更新
            if (isset($request->user_image)) {
                $userImageService = new UserImageService();

                // すでにサムネイル画像がある、かつその画像がnoimage以外なら
                if (!empty($lastThumbnailId) && $user->thumbnail_id != 1) {
                    $userImageService->deleteImages([$lastThumbnailId]);
                }

                $image = $userImageService->saveImage($request->file('user_image'), [
                    'user_id' => $user->id,
                ]);
                $user->thumbnail_id = $image->id;
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
    }
}
