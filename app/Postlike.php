<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Postlike extends Model
{
    protected $fillable = [
        'user_id', 'post_id'
    ];
}
