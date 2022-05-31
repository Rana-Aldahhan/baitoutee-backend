<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Chef extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable ,SoftDeletes;
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
        /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
     protected $appends = [];

    /**
     * relationships
     */
    public function orders(){
        return $this->hasMany(Order::class);
    }
    public function subscriptions(){
        return $this->hasMany(Subscription::class);
    }
    public function meals(){
        return $this->hasMany(Meal::class);
    }
    public function location(){
        return $this->belongsTo(Location::class);
    }
    public function chefJoinRequest(){
        return $this->belongsTo(ChefJoinRequest::class);
    }

}
