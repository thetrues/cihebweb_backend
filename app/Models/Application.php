<?php

namespace App\Models;

use App\Models\Career;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    protected $fillable = [
        'career_id',
        'name',
        'email',
        'phone',
        'cover_letter',
        'resume',
        'status',
    ];
    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }
}
