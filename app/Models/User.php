<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

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
    protected $hidden = ['location_id', 'email_verified_at', 'birth_date', 'gender',
        'national_id', 'campus_card_id', 'campus_unit_number',
        'study_specialty', 'study_year', 'approved_at', 'deleted_at', 'created_at', 'updated_at'];

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
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
    public function joinRequests()
    {
        return $this->hasMany(UserJoinRequest::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class)->withPivot('notes', 'paid', 'total_cost', 'delivery_cost_per_day')->withTimestamps();
    }
    public function outcomingReports()
    {
        return $this->morphMany(Reports::class, 'sendable');
    }
    public function incomingReports()
    {
        return $this->morphMany(Reports::class, 'receivable');
    }
    public function savedMeals()
    {
        return $this->belongsToMany(Meal::class, 'meals_saved_list', 'user_id', 'meal_id');
    }

}
