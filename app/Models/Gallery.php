<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $table = 'gallery';
    protected $fillable = [
        'title',
        'subtitle',
        'category',
        'image_path',
        'video_path',
        'description'
    ];
}
