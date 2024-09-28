<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryDiscount extends Model
{
    use HasFactory;

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where(['status' => 0])->where('start_date', '<=', now()->format('Y-m-d'))->where('expire_date', '>=', now()->format('Y-m-d'));
    }
}
