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
    protected $appends = ['access_token'];

    protected function accessToken(): Attribute
    {
        return new Attribute(
            get: fn () => $this->getFirstToken(),
        );
    }

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
     /**
     * getters
     */
    public function getFirstToken()
    {
        return $this->tokens()->first()->token;
    }
}
