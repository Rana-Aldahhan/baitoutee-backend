<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Deliveryman extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable ,SoftDeletes;
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * relationships
     */
    public function deliverymanJoinRequest(){
        return $this->belongsTo(DeliverymanJoinRequest::class);
    }
    public function deliveries(){
        return $this->hasMany(Delivery::class);
    }
}
