<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    use HasFactory;

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'parent_id');
    }

    public function status()
    {
        return $this->query()->where('status', 0);
    }

    public function childes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ServiceCategory::class, 'parent_id');
    }

    public function Services(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Service::class, 'child_category_id','id');
    }
}
