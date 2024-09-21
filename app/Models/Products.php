<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Order_details,ProductReview};
use Illuminate\Support\Facades\DB;

class Products extends Model
{
    use HasFactory;

    public function all_rating(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductReview::class, 'product_id', 'id')
            ->select(DB::raw('avg(rating) average, product_id'))
            ->groupBy('product_id');
    }

    public function active_reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductReview::class, 'product_id', 'id')->where(['is_active' => 1])->latest();
    }

    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductReview::class, 'product_id', 'id')->latest();
    }

    public function order_details(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order_details::class, 'product_id', 'id');
    }

    public function status()
    {
        return $this->query()->where('status', 0);
    }
}
