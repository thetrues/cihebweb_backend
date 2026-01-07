<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Initiative extends Model
{
    protected $table = 'initiatives';
    protected $fillable = [
        'title',
        'subtitle',
        'image_path',
        'video_path',
        'short_description',
        'detailed_description'
    ];
}
