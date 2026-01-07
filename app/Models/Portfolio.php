<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    protected $table = 'portfolio';
    protected $fillable = [
        'title',
        'subtitle',
        'image_path',
        'video_path',
        'file_path',
        'short_description',
        'detailed_description'
    ];
}
