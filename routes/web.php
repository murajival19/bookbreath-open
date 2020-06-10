<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', 'BookController@index')->name('books.index');
Route::get('books/search', 'BookController@search')->name('books.search');
Route::post('books/search', 'BookController@search')->name('books.search');
Route::get('books/externalSearch', 'BookController@externalSearch')->name('books.externalSearch');
Route::post('books/externalSearch', 'BookController@externalSearch')->name('books.externalSearch');
Route::get('books/librarySearch', 'BookController@librarySearch')->name('books.librarySearch');
Route::post('books/librarySearch', 'BookController@librarySearch')->name('books.librarySearch');
Route::get('/terms', 'HomeController@terms')->name('home.terms');
Route::get('/policy', 'HomeController@policy')->name('home.policy');
Route::get('books/buyAmazon', 'BookController@buyAmazon')->name('books.buyAmazon');
Route::get('books/buyRakuten', 'BookController@buyRakuten')->name('books.buyRakuten');
Route::get('books/buyYahoo', 'BookController@buyYahoo')->name('books.buyYahoo');
Route::get('books/howToUse', 'HomeController@howToUse')->name('home.howToUse');

// 新規投稿、編集はログイン必須
Route::get('posts/create', 'PostController@create')->name('posts.create')->middleware('auth');
Route::get('posts/{post}/edit', 'PostController@edit')->name('posts.edit')->middleware('auth');
Route::get('posts/delete/{post}', 'PostController@delete')->name('posts.delete')->middleware('auth');
Route::get('users/{user}/edit', 'UserController@edit')->name('users.edit')->middleware('auth');
Route::get('books/create', 'BookController@create')->name('books.create')->middleware('auth');
Route::post('books/create', 'BookController@create')->name('books.create')->middleware('auth');
Route::get('books/library', 'BookController@library')->name('books.library')->middleware('auth');

Route::get('posts/search', 'PostController@search')->name('posts.search');
Route::post('posts/search', 'PostController@search')->name('posts.search');
Route::get('posts/twitter/{post}', 'PostController@twitter')->name('posts.twitter');

Route::resource('books', 'BookController', ['except' => ['index', 'search', 'create']]);
Route::resource('posts', 'PostController', ['except' => ['create', 'edit']]);
Route::resource('users', 'UserController', ['except' => ['edit']]);
