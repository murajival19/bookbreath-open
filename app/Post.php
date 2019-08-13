<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id', 'book_id', 'content', 'reply_id', 'reference_id'
    ];

    public function user(){
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function book(){
        return $this->belongsTo(\App\Book::class, 'book_id');
    }

    // public function comment(){
    //     return $this->hasMany(\App\Comment::class, 'post_id', 'id');
    // }

    // 子コメント(reply)をリレーション
    public function post_children(){
        return $this->hasMany(\App\Post::class, 'reply_id', 'id');
    }

    // 親コメント(reply)をリレーション
    public function post_parent(){
        return $this->hasOne(\App\Post::class, 'id', 'reply_id');
    }

    // referenceをリレーション
    public function post_reference(){
        return $this->hasOne(\App\Post::class, 'id', 'reference_id');
    }

    public function image(){
        return $this->hasMany(\App\Image::class, 'post_id', 'id');
    }

    public function post(){
        return $this->hasOne(\App\Post::class, 'id', 'id');
    }

    public function thumbnail_image(){
        return $this->hasManyThrough(
            \App\Image::class,
            \App\User::class,
            'user_id',
            'id',
            null,
            'thumbnail_id'
        );
    }

    public function postlike(){
        return $this->hasMany(\App\Postlike::class, 'post_id', 'id');
    }
}
