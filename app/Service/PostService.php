<?php

namespace App\Service;

use App\Repository\PostRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostService
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
     * 投稿の画像に関するサービスクラスのインスタンス
     *
     * @var \App\Service\PostImageService
     */
    private $postImageService;

    /**
     * twitterのベースURL
     *
     * @var string
     */
    private $twitterBaseUrl;

    /**
     * コンストラクタ
     *
     * @param PostRepository $postRepository
     * @param LikeService $likeService
     * @param PostImageService $postImageService
     */
    public function __construct(PostRepository $postRepository, LikeService $likeService, PostImageService $postImageService)
    {
        $this->postRepository = $postRepository;
        $this->likeService = $likeService;
        $this->postImageService = $postImageService;
        $this->twitterBaseUrl = 'https://twitter.com/intent/tweet?';
    }

    /**
     * すべての投稿を取得します。
     *
     * @return \App\Post
     */
    public function getPosts()
    {
        $posts = $this->postRepository->getPostsDesc()->paginate(10);

        // likeカウントとlikedの判定（post,複数）
        foreach ($posts as $post) {
            $this->likeService->postLikedJudge($post);
        }
        return $posts;
    }

    /**
     * 投稿作成のためのパラメータをセットします。
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function setCreatePostParam(\Illuminate\Http\Request $request)
    {
        $param = ['user' => Auth::user()];

        // book_idをセット
        if (isset($request->book_id)) {
            $param += ['book_id' => $request->book_id];
        }

        // replyコメントがある時
        if (isset($request->reply_id)) {
            $param += ['reply_id' => $request->reply_id];
            $post = $this->postRepository->getReplyPost($request->reply_id);
            if (isset($post->book_id)) {
                $param += ['book_id' => $post->book_id];
            }
            // referrenceコメントがある時
        } elseif (isset($request->reference_id)) {
            $param += ['reference_id' => $request->reference_id];
            $post = $this->postRepository->getReferencePost($request->reference_id);
            if (isset($post->book_id)) {
                $param += ['book_id' => $post->book_id];
            }
        }
        return $param;
    }

    /**
     * 投稿を保存します。
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function createPost(\Illuminate\Http\Request $request)
    {
        DB::transaction(function () use ($request) {
            // 投稿を保存
            $post = $this->postRepository->savePost($request->all());
            // 画像を保存
            if (isset($request->post_image)) {
                $this->postImageService->saveImage($request->file('post_image'), [
                    'user_id' => Auth::id(),
                    'post_id' => $post->id,
                ]);
            }
        });
    }

    /**
     * 投稿のいいねを取得し、セットします。。
     *
     * @param \App\Post $post
     * @return void
     */
    public function setPostLiked(\App\Post $post)
    {
        // likeカウントとlikedの判定（post,単数）
        $this->likeService->postLikedJudge($post);

        // likeカウントとlikedの判定（post_parent,単数）
        if (isset($post->post_parent)) {
            $this->likeService->postLikedJudge(($post->post_parent));
        }

        // likeカウントとlikedの判定（post_children,複数）
        if (isset($post->post_children)) {
            foreach ($post->post_children as $post_child) {
                $this->likeService->postLikedJudge($post_child);
            }
        }
    }

    /**
     * 返信投稿のページネーションを設定します。
     *
     * @param \Illuminate\Database\Eloquent\Collection $postChildren
     * @param \Illuminate\Http\Request $request
     * @param integer $pageCount
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function setPostChildrenPaginate(\Illuminate\Database\Eloquent\Collection $postChildren, \Illuminate\Http\Request $request, int $pageCount)
    {
        // ページネーションの設定
        $postChildren =  new LengthAwarePaginator(
            $postChildren->forPage($request->page, $pageCount),
            count($postChildren),
            $pageCount,
            $request->page,
            ['path' => $request->url()],
        );
        return $postChildren;
    }

    /**
     * 投稿を更新します。
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Post $post
     * @return void
     */
    public function updatePost(\Illuminate\Http\Request $request, \App\Post $post)
    {
        DB::transaction(function () use ($request, $post) {
            // 投稿の保存
            $post->fill($request->all())->save();

            // 削除する画像がある場合
            if (isset($request->image_delete)) {
                $this->postImageService->deleteImages($request->image_delete);
            }

            // 追加する画像がある場合
            if (isset($request->post_image)) {
                $this->postImageService->saveImage($request->file('post_image'), [
                    'user_id' => Auth::id(),
                    'post_id' => $post->id,
                ]);
            }
        });
    }

    /**
     * 投稿を削除します。
     *
     * @param integer $postId
     * @return void
     */
    public function deletePost(int $postId)
    {
        // 画像を削除
        $postImageService = new PostImageService();
        $postImageService->deletePostImages($postId);

        // 投稿を削除
        $postRepository = new PostRepository();
        $postRepository->deletePost($postId);
    }

    /**
     * 投稿を検索します。
     *
     * @param string $searchWord
     * @return mixed
     */
    public function searchPosts(string $searchWord, int $pageCount)
    {
        $posts = $this->postRepository->getPostsSearchContent($searchWord)->paginate($pageCount);

        // likeカウントとlikedの判定（post,複数）
        foreach ($posts as $post) {
            $this->likeService->postLikedJudge($post);
        }

        return $posts;
    }

    /**
     * 投稿をtwitterへシェアします。
     *
     * @param \App\Post $post
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function sendTwitter(\App\Post $post, \Illuminate\Http\Request $request)
    {
        $url = 'url=' . $request->url . '/posts/' . $post->id;
        $text = '&text=' . $post->content . ' by ' . $request->user_name;
        $text = preg_replace('/(?:\n|\r|\r\n)/', '', $text);

        if (isset($request->book_title)) {
            $hashtags = '&hashtags=' . $request->book_title;
            return $this->twitterBaseUrl . $url . $text . $hashtags;
        } else {
            return $this->twitterBaseUrl . $url . $text;
        }
    }
}
