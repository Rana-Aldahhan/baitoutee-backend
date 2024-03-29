<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meal extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory,Searchable,SoftDeletes;
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at', 'chef_id'];
    protected $with = ['chef:id,name,fcm_token', 'category:name,id'];
    protected $casts = [
        'approved' => 'boolean',
        'is_available' => 'boolean',
        'rating'=>'double'
    ];
    /**
     * relationships
     */
    public function category()
    {
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
        return $this->belongsTo(Chef::class)->withTrashed();
    }
    public function savingUsers()
    {
        return $this->belongsToMany(User::class, 'meals_saved_list', 'user_id', 'meal_id');
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

    /**
     * search
     */
    public function toSearchableArray()
    {
        $array = [
            'name'=>$this->name,
            'ingredients'=>$this->ingredients,
        ];

        return $array;
    }

}
