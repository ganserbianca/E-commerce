<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'cep',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'uf',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
