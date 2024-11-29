<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Vendor extends Authenticatable
{
    use HasFactory, HasApiTokens;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function status()
    {
        return $this->query()->where('is_block', 0);
    }

    public function Scopestatus()
    {
        return $this->query()->where('is_block', 0);
    }

    public function verify()
    {
        return $this->query()->where('is_verify', 0);
    }
    
    public function vendororders(){
        return $this->hasMany(Order::class,'vender_id','id');
    }
    
    public function vendorproducts(){
        return $this->hasMany(Products::class,'vender_id','id');
    }

    static function total_order_amount($vendor_id)
    {
        $total_amount = 0;
        $vendor = Vendor::where(['id' => $vendor_id])->with('vendororders')->first();
        foreach ($vendor->vendororders as $order)
        {
            $total_amount += $order->order_amount;
        }
        return $total_amount;
    }

    public function vendors(){
        return $this->hasone(Products::class,'vender_id','id');
    }

   
}
