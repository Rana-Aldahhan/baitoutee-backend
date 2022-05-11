<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    /**
     * relationships
     */
    public function user()
    {
        $this->belongsTo(User::class,'user_id');
    }
    public function chef()
    {
        $this->belongsTo(Chef::class,'chef_id');
    }
    public function delivery()
    {
        $this->belongsTo(Delivery::class,'delivery_id');
    }
    public function deliveryman()//TODO check if it is valid
    {
        $this->hasOneThrough(Deliveryman::class,Delivery::class,'deliveryman_id');
    }
    public function subscription()
    {
        $this->belongsTo(Subscription::class,'subscription_id');
    }
    public function meals(){
        $this->belongsToMany(Meal::class,'meals_order','order_id','meal_id');
    }
    public function reports(){
        $this->hasMany(Reports::class);
    }
    
}
