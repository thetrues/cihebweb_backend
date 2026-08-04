<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuccessStory extends Model
{
    protected $table = 'success_stories';
    protected $fillable = [
        'title',
        'description',
        'impact',
        'beneficiaries',
        'quote',
        'author',
        'position',
        'is_active',
        'image',
    ];
}
