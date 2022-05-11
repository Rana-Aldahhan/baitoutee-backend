<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable ,SoftDeletes;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [

    ];

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
    public function location()
    {
        $this->belongsTo(Location::class,'location_id');
    }
    public function joinRequests(){
        $this->hasMany(UserJoinRequest::class);
    }
    public function orders(){
        $this->hasMany(Order::class);
    }
    public function subscriptions(){
        $this->belongsToMany(Subscription::class)->withPivot('notes','paid','total_cost','delivery_cost_per_day')->withTimestamps();
    }
    public function outcomingReports(){
        $this->morphMany(Reports::class,'sendable');
    }
    public function incomingReports(){
        $this->morphMany(Reports::class,'receivable');
    }
    public function savedMeals(){
        $this->belongsToMany(Meal::class,'meals_saved_list','user_id','meal_id');
    }
}
