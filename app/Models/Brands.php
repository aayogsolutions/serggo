<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Products;

class Brands extends Model
{
    use HasFactory;

    public function childes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Products::class, 'brand_name', 'name');
    }

    public function status()
    {
        return $this->query()->where('status', 0);
    }
}
