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

    public function Services()
    {
        return $this->belongsTo(ServiceCategory::class,'child_category_id');
    }
}
