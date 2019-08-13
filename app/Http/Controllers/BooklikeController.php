<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Booklike;

class BooklikeController extends Controller
{
    public function like(Request $request)
    {
        $booklike = new Booklike;
        $booklike->user_id = $request->user_id;
        $booklike->book_id = $request->book_id;
        $booklike->save();

        $likeCount = count(Booklike::where('book_id', $request->book_id)->get());
        return response()->json(['likeCount' => $likeCount]);
    }

    public function unlike(Booklike $booklike, Request $request)
    {
        $delete_booklike = $booklike->where('user_id', $request->user_id)->where('book_id', $request->book_id)->first();
        $delete_booklike->delete();

        $likeCount = count(Booklike::where('book_id', $request->book_id)->get());
        return response()->json(['likeCount' => $likeCount]);
    }
}
