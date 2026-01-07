<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';
    protected $fillable = [
        'title',
        'subtitle',
        'image_path',
        'video_path',
        'short_description',
        'detailed_description',
        'location',
        'partner_organizations',
        'start_date',
        'end_date',
        'funding_source',
        'budget',
        'status'
    ];
}
