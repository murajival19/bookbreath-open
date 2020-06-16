<?php

namespace App\Service;

use App\Repository\PostRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * ユーザーに関するサービスクラス
 */
class UserService
{
    /**
     * 投稿に関するリポジトリクラスのインスタンス
     *
     * @var \App\Repository\PostRepository
     */
    private $postRepository;

    /**
     * いいねに関するサービスクラスのインスタンス
     *
     * @var \App\Service\LikeService
     */
    private $likeService;

    /**
     * プロフィール編集時の画像に関するサービスクラスのインスタンス
     *
     * @var \App\Service\UserImageService
     */
    private $userImageService;

    /**
     * コンストラクタ
     *
     * @param PostRepository $postRepository
     * @param LikeService $likeService
     * @param UserImageService $userImageService
     */
    public function __construct(PostRepository $postRepository, LikeService $likeService, UserImageService $userImageService)
    {
        $this->postRepository = $postRepository;
        $this->likeService = $likeService;
        $this->userImageService = $userImageService;
    }

    /**
     * 指定user_idの投稿を取得します。
     *
     * @param int $userId
     * @return \Illuminate\Pagination\LengthAwarePaginator 
     */
    public function getPosts(int $userId)
    {
        // $postRepository = new PostRepository();
        $posts = $this->postRepository->getPostsUserId($userId)->paginate(10);

        // likeカウントとlikedの判定（post,複数）
        // $likeService = new LikeService();
        foreach ($posts as $post) {
            $this->likeService->postLikedJudge($post);
        }
        return $posts;
    }

    /**
     * 編集用のユーザー情報をセットします。
     *
     * @param \App\User $user
     * @return void
     */
    public function setUserForEdit(\App\User $user)
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
    }

    /**
     * ユーザーのプロフィール情報を更新します。
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @return void
     */
    public function updateUser(\Illuminate\Http\Request $request, \App\User $user)
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
                // $userImageService = new UserImageService();

                // すでにサムネイル画像がある、かつその画像がnoimage以外なら
                if (!empty($lastThumbnailId) && $user->thumbnail_id != 1) {
                    $this->userImageService->deleteImages([$lastThumbnailId]);
                }

                $image = $this->userImageService->saveImage($request->file('user_image'), [
                    'user_id' => $user->id,
                ]);
                $user->thumbnail_id = $image->id;
            } else {
                $user->thumbnail_id = $lastThumbnailId;
            }

            $user->save();
        });
    }
}
