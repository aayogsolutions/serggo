<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\providers;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'wallet_balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'number_verified_at' => 'datetime',
            'otp_expired_at' => 'datetime',
        ];
    }

    public function orders(){
        return $this->hasMany(Order::class,'user_id','id');
    }

    public function providers()
    {
        return $this->hasMany(providers::class,'user_id','id');
    }

    public function transaction()
    {
        return $this->hasMany(WalletTranscation::class,'user_id','id');
    }

    public function status()
    {
        return $this->query()->where('is_block', 0);
    }

    static function total_order_amount($customer_id)
    {
        $total_amount = 0;
        $customer = User::where(['id' => $customer_id])->first();
        foreach ($customer->orders as $order){
            $total_amount += $order->order_amount;
        }
        return $total_amount;
    }

    public function address()
    {
        return $this->hasMany(CustomerAddresses::class,'user_id','id');
    }
}
