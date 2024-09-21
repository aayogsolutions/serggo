<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryReviews extends Model
{
    use HasFactory;

    public function status()
    {
        return $this->query()->where('status', 0);
    }
}
