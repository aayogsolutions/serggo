<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTimeSlot extends Model
{
    use HasFactory;

    public function ServiceTimeSlot(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class, 'delivery_timeslot_id','id');
    }
}
