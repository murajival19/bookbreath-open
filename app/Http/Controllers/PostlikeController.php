<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Postlike;

class PostlikeController extends Controller
{
    public function like(Request $request)
    {
        $postlike = new Postlike;
        $postlike->user_id = $request->user_id;
        $postlike->post_id = $request->post_id;
        $postlike->save();

        $likeCount = count(Postlike::where('post_id', $request->post_id)->get());
        return response()->json(['likeCount' => $likeCount]);
    }

    public function unlike(Postlike $postlike, Request $request)
    {
        $delete_postlike = $postlike->where('user_id', $request->user_id)->where('post_id', $request->post_id)->first();
        $delete_postlike->delete();

        $likeCount = count(Postlike::where('post_id', $request->post_id)->get());
        return response()->json(['likeCount' => $likeCount]);
    }
}
