<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'address_id',
        'status',
        'total_amount',
        'ordered_at',
        'paid_at',
        'order_street',
        'order_number',
        'order_cep',
        'order_neighborhood',
        'order_city',
        'order_uf',
        'order_complement',
    ];

    protected function casts(): array
    {
        return [
            'ordered_at'   => 'datetime',
            'paid_at'      => 'datetime',
            'total_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}