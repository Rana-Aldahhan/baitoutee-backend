<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Deliveryman extends Authenticatable
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    protected $guarded = [];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['birth_date', 'gender', 'deliveryman_join_request_id', 'transportation_type', 'work_days',
        'work_hours_from', 'work_hours_to', 'current_longitude', 'current_latitude', 'balance', 'is_available',
        'approved_at', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'current_longitude'=>'double',
        'current_latitude'=>'double',
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
    public function deliverymanJoinRequest()
    {
        return $this->belongsTo(DeliverymanJoinRequest::class);
    }
    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }
    public function deliveryOrders()
    {
        return $this->hasManyThrough(Order::class, Delivery::class, 'deliveryman_id', 'delivery_id');
    }

}
