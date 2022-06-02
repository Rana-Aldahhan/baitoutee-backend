<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with=['chef:id,name'];
    /**
     * relationships
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function meals()
    {
        return $this->belongsToMany(Meal::class);
    }
    public function chef()
    {
        return $this->belongsTo(Chef::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('notes','paid','total_cost','delivery_cost_per_day')->withTimestamps();
    }
}
