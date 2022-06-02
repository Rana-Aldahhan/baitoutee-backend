<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at','chef_id'];
    protected $with=['chef:id,name','category:name,id'];
    protected $casts = [
        'approved' => 'boolean',
        'is_available' => 'boolean',
    ];
    /**
     * relationships
     */
    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }
    public function priceChangeRequest()
    {
        return $this->hasMany(PriceChangeRequest::class);
    }
    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class);
    }
    public function chef()
    {
        return $this->belongsTo(Chef::class);
    }
    public function savingUsers()
    {
        return $this->belongsToMany(User::class);
    }
    /**
     * scopes
     */
    /**
     * Scope a query to only include approved meals
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

}
