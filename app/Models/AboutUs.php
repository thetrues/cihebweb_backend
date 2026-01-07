<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutUs extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'image_path',
        'video_path',
        'description',
        'mission',
        'vision',
        'values',
        'established_year',
        'team_members',
        'regions',
        'contact_email',
        'contact_phone',
        'social_media_links',
        'awards',
        'founder',
        'location',
    ];
}
