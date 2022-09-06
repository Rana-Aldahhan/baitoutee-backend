<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    /**
     * The attributes that are not mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];
    protected $dates = ['prepared_at','created_at'];
    protected $casts = ['total_cost' => 'double','meals_cost'=>'double','profit'=>'double'];
    /**
     * relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')
        ->withTrashed();
    }
    public function chef()
    {
        return $this->belongsTo(Chef::class, 'chef_id')
        ->withTrashed();
    }
    public function delivery()
    {
        return $this->belongsTo(Delivery::class, 'delivery_id');
    }
    public function deliveryman()
    { //not working
        return $this->hasManyThrough(Deliveryman::class, Delivery::class, 'deliveryman_id');
    }
    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id')
        ->withTrashed();
    }
    public function meals()
    {
        return $this->belongsToMany(Meal::class, 'meal_order', 'order_id', 'meal_id')
            ->withPivot('quantity', 'notes', 'meal_rate', 'meal_rate_notes')->withTimestamps()
            ->withTrashed();
    }
    public function reports()
    {
        return $this->hasMany(Reports::class);
    }

}
