<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $table = 'sliders';
    protected $fillable = [
        'title',
        'subtitle',
        'image_path',
        'video_path',
        'description',
        'link',
        'display_order',
        'is_active',
    ];
}
