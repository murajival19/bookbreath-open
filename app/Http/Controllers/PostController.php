<?php

namespace App\Http\Controllers;

use App\Post;
use App\Http\Requests\PostRequest;
use App\Service\PostService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/**
 * 投稿に関するコントローラクラス
 */
class PostController extends Controller
{
    /**
     * 投稿に関するサービスクラスのインスタンス
     *
     * @var \App\Service\PostService
     */
    private $postService;

    /**
     * コンストラクタ
     *
     * @param PostService $postService
     */
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = $this->postService->getPosts();
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
        $param = $this->postService->setCreatePostParam($request);
        return view('posts.create', $param);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $this->postService->createPost($request);

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
        $this->postService->setPostLiked($post);
        // 返信投稿のページネーションの設定
        $postChildrenPaginate = $this->postService->setPostChildrenPaginate($post->post_children, $request, 5);

        $response = [
            'post' => $post,
            'post_children' => $postChildrenPaginate,
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
        return view('posts.edit', [
            'post' => $post,
            'user' => Auth::user(),
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
        $this->postService->updatePost($request, $post);
        return redirect()->route('posts.show', $post->id);
    }

    /**
     * 投稿を削除します。
     *
     * @param Post $post
     * @return \Illuminate\Http\Response
     */
    public function delete(Post $post)
    {
        $this->postService->deletePost($post->id);
        return redirect('/posts');
    }

    /**
     * 投稿を検索します。
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $posts = $this->postService->searchPosts($request->search, 10);
        return view('posts.search', [
            'posts' => $posts,
            'search_query' => $request->search,
            'search_count' => $posts->count(),
        ]);
    }

    /**
     * 投稿をtwitterにシェアします。
     *
     * @param Post $post
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function twitter(Post $post, Request $request)
    {
        return redirect()->away($this->postService->sendTwitter($post, $request));
    }
}
