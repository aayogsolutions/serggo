<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AMCPlan extends Model
{
    use HasFactory;

    public function PlanChild(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AMCPlanServices::class, 'plan_id');
    }
}
