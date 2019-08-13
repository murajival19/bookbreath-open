<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'book_title', 'author', 'publishedDate', 'isbn_13', 'book_description', 'book_image_url', 'category_id', 'content_count'
    ];

    public function category(){
        return $this->belongsTo(\App\Category::class);
    }

    public function book(){
        return $this->hasOne(\App\Book::class, 'id', 'id');
    }

    public function post(){
        return $this->hasMany(\App\Post::class, 'book_id', 'id');
    }

    public function user(){
        return $this->hasManyThrough(
            \App\User::class,
            \App\Post::class,
            'book_id',
            'id',
            null,
            'user_id'
        );
    }

    public function image(){
        return $this->hasManyThrough(
            \App\Image::class,
            \App\Post::class
        );
    }

    public function booklike(){
        return $this->hasMany(\App\Booklike::class, 'book_id', 'id');
    }
}
