<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'symbol',
        'name',
        'price',
        'change',
        'trend',
        'user_id'
    ];

    /**
     * Get the user that owns the stock.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
