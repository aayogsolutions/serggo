<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceReview extends Model
{
    use HasFactory;

    public function scopeStatusStatic($qurey)
    {
        return $this->query()->where('status', 0);
    }
}
