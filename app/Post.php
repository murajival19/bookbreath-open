<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'content',
        'reply_id',
        'reference_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    // 子コメント(reply)をリレーション
    public function post_children()
    {
        return $this->hasMany(Post::class, 'reply_id', 'id');
    }

    // 親コメント(reply)をリレーション
    public function post_parent()
    {
        return $this->hasOne(Post::class, 'id', 'reply_id');
    }

    // referenceをリレーション
    public function post_reference()
    {
        return $this->hasOne(Post::class, 'id', 'reference_id');
    }

    public function image()
    {
        return $this->hasMany(Image::class, 'post_id', 'id');
    }

    public function post()
    {
        return $this->hasOne(Post::class, 'id', 'id');
    }

    public function thumbnail_image()
    {
        return $this->hasManyThrough(
            Image::class,
            User::class,
            'user_id',
            'id',
            null,
            'thumbnail_id',
        );
    }

    public function postlike()
    {
        return $this->hasMany(Postlike::class, 'post_id', 'id');
    }
}
