<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\PostLikeRepository;

class PostlikeController extends Controller
{
    public function like(Request $request)
    {
        $postLikeRepository = new PostLikeRepository();
        $postLikeRepository->savePostLike([
            'user_id' => $request->user_id,
            'post_id' => $request->post_id,
        ]);
        $likeCount = count($postLikeRepository->getPostLike($request->post_id)->get());
        return response()->json(['likeCount' => $likeCount]);
    }

    public function unlike(Request $request)
    {
        $postLikeRepository = new PostLikeRepository();
        $postLikeRepository->deletePostLike($request->user_id, $request->post_id);
        $likeCount = count($postLikeRepository->getPostLike($request->post_id)->get());
        return response()->json(['likeCount' => $likeCount]);
    }
}
