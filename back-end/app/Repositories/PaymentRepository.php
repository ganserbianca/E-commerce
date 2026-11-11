<?php

namespace App\Repositories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepository
{
    public function findByOrder(int $orderId): ?Payment
    {
        return Payment::where('order_id', $orderId)->first();
    }

    public function create(array $paymentData): Payment
    {
        return Payment::create($paymentData);
    }
}
