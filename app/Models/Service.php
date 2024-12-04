<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    public function status()
    {
        return $this->query()->where('status', 0);
    }

    public function Services() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class,'child_category_id');
    }

    public function Subcategory() : \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ServiceCategory::class,'id' ,'sub_category_id');
    }

    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ServiceReview::class, 'service_man_id', 'id')->latest();
    }
}
