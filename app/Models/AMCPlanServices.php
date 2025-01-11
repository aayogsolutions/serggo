<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AMCPlanServices extends Model
{
    use HasFactory;

    public function PlanChild(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AMCPlan::class, 'plan_id');
    }
}
