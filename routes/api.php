<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/booklikes/like', 'BooklikeController@like')->name('booklikes.like');
Route::post('/booklikes/unlike', 'BooklikeController@unlike')->name('booklikes.unlike');
Route::post('/postlikes/like', 'PostlikeController@like')->name('postlikes.like');
Route::post('/postlikes/unlike', 'PostlikeController@unlike')->name('postlikes.unlike');
