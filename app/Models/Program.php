<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'programs';
    protected $fillable = [
        'title',
        'description',
        'status',
        'start_date',
        'is_active'
    ];
}
