<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'name_id',
        'email',
        'password',
        'thumbnail_id',
        'user_description',
        'user_website',
        'user_twitter',
        'user_facebook',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id');
    }

    public function post()
    {
        return $this->hasMany(Post::class, 'user_id', 'id');
    }

    public function book()
    {
        return $this->hasManyThrough(
            Book::class,
            Post::class,
            'user_id',
            'id',
            null,
            'book_id'
        );
    }

    public function image()
    {
        return $this->hasMany(Image::class, 'user_id', 'id');
    }

    public function thumbnail_image()
    {
        return $this->belongsTo(Image::class, 'thumbnail_id', 'id');
    }

    public function background_image()
    {
        return $this->belongsTo(Image::class, 'background_id', 'id');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
