<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class Coupons extends Model
{
    use HasFactory;

    public function status()
    {
        return $this->query()->where('status', 0);
    }

    public function order(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class, 'coupon_code', 'code');
    }
}
