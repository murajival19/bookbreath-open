<?php

namespace App\Http\Controllers;

use App\Post;
use App\Http\Requests\PostRequest;
use App\Repository\PostRepository;
use App\Service\LikeService;
use App\Service\PostImageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * 投稿に関するコントローラクラス
 */
class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $postRepository = new PostRepository();
        $posts = $postRepository->getPostsDesc()->paginate(10);

        // likeカウントとlikedの判定（post,複数）
        $likeService = new LikeService();
        foreach ($posts as $post) {
            $likeService->postLikedJudge($post);
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
        $response = ['user' => $user];

        // book_idをセット
        if (isset($request->book_id)) {
            $response += ['book_id' => $request->book_id];
        }

        $postRepository = new PostRepository();
        // replyコメントがある時
        if (isset($request->reply_id)) {
            $response += ['reply_id' => $request->reply_id];
            $post = $postRepository->getReplyPost($request->reply_id);
            if (isset($post->book_id)) {
                $response += ['book_id' => $post->book_id];
            }
            // referrenceコメントがある時
        } elseif (isset($request->reference_id)) {
            $response += ['reference_id' => $request->reference_id];
            $post = $postRepository->getReferencePost($request->reference_id);
            if (isset($post->book_id)) {
                $response += ['book_id' => $post->book_id];
            }
        }

        return view('posts.create', $response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $postRepository = new PostRepository();
        DB::transaction(function () use ($postRepository, $request) {
            // 投稿を保存
            $post = $postRepository->savePost($request->all());
            // 画像を保存
            if (isset($request->post_image)) {
                $postImageService = new PostImageService();
                $postImageService->saveImage($request->file('post_image'), [
                    'user_id' => Auth::id(),
                    'post_id' => $post->id,
                ]);
            }
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
        $postRepository = new PostRepository();

        // likeカウントとlikedの判定（post,単数）
        $likeService = new LikeService();
        $likeService->postLikedJudge($post);

        // likeカウントとlikedの判定（post_parent,単数）
        if (isset($post->post_parent)) {
            $likeService->postLikedJudge(($post->post_parent));
        }

        // likeカウントとlikedの判定（post_children,複数）
        if (isset($post->post_children)) {
            // $post->load('post_children.postlike');
            foreach ($post->post_children as $post_child) {
                $likeService->postLikedJudge($post_child);
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

        $response = [
            'post' => $post,
            'post_children' => $post->post_children, // ページネーション用
        ];

        return view('posts.show', $response);
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
        if (intval($request->user_id) !== $post->user->id) {
            return redirect('posts');
        }

        DB::transaction(function () use ($request, $post) {
            // 投稿の保存
            $post->fill($request->all())->save();

            $postImageService = new PostImageService();
            // 削除する画像がある場合
            if (isset($request->image_delete)) {
                $postImageService->deleteImages($request->image_delete);
            }

            // 追加する画像がある場合
            if (isset($request->post_image)) {
                $postImageService->saveImage($request->file('post_image'), [
                    'user_id' => Auth::id(),
                    'post_id' => $post->id,
                ]);
            }
        });

        return redirect()->route('posts.show', $post->id);
    }

    public function delete(Post $post)
    {
        // 画像を削除
        $postImageService = new PostImageService();
        $postImageService->deletePostImages($post->id);
        // 投稿を削除
        $postRepository = new PostRepository();
        $postRepository->deletePost($post->id);

        return redirect('/posts');
    }

    public function search(Request $request)
    {
        $postRepository = new PostRepository();
        $posts = $postRepository->getPostsSearchContent($request->search)->paginate(10);

        // likeカウントとlikedの判定（post,複数）
        $likeService = new LikeService();
        foreach ($posts as $post) {
            $likeService->postLikedJudge($post);
        }

        return view('posts.search', [
            'posts' => $posts,
            'search_query' => $request->search,
            'search_count' => $posts->count(),
        ]);
    }

    public function twitter(Post $post, Request $request)
    {
        $baseUrl = 'https://twitter.com/intent/tweet?';
        $url = 'url=' . $request->url . '/posts/' . $post->id;
        $text = '&text=' . $post->content . ' by ' . $request->user_name;
        $text = preg_replace('/(?:\n|\r|\r\n)/', '', $text);

        if (isset($request->book_title)) {
            $hashtags = '&hashtags=' . $request->book_title;
            $tweetShere = $baseUrl . $url . $text . $hashtags;
        } else {
            $tweetShere = $baseUrl . $url . $text;
        }

        return redirect()->away($tweetShere);
    }
}
