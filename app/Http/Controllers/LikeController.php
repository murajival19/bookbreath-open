<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Like;

class LikeController extends Controller
{
    public function like(Request $request)
    {
        if (isset($request->userId)) {
            return route('login');
        }

        $like = new Like;
        $like->user_id = $request->userId;
        $like->book_id = $request->bookId;
        $like->save();

        return response();
    }
}
