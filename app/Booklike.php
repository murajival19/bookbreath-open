<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booklike extends Model
{
    protected $fillable = [
        'user_id', 'book_id'
    ];
}
