<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StarGeneration extends Model
{
    protected $fillable = [
        'user_id',
        'jumlah',
        'tipe',
        'hasil',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}