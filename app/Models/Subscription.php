<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory,SoftDeletes;
    protected $guarded = [];
    protected $with=['chef:id,name,profile_picture,fcm_token'];
    protected $casts = [ 'is_available' => 'boolean' ];
    protected $hidden=['created_at','updated_at','max_subscribers','meals_cost'];
    /**
     * relationships
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function meals()
    {
        return $this->belongsToMany(Meal::class)->withPivot('day_number')
        ->withTrashed();
    }
    public function chef()
    {
        return $this->belongsTo(Chef::class)
        ->withTrashed();
    }
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('notes','paid','total_cost','delivery_cost_per_day')->withTimestamps();
    }
}
