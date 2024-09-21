<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisplaySection extends Model
{
    use HasFactory;

    public function childes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DisplaySectionContent::class, 'section_id', 'id');
    }

    public function status()
    {
        return $this->query()->where('status', 0);
    }
}
