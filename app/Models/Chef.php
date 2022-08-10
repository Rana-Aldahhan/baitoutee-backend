<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;
// use Dyrynda\Database\Support\CascadeSoftDeletes;

class Chef extends Authenticatable
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasApiTokens, HasFactory, Notifiable ,SoftDeletes,Searchable;
    // use CascadeSoftDeletes;
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_available'=>'boolean'
    ];
        /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
     protected $appends = [];

     /**
      * soft delete on cascade
      */
      protected $cascadeDeletes = ['meals'];
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
     * searchable attributes
     */
    public function toSearchableArray()
    {
        $array = [
            'name'=>$this->name,
        ];

        return $array;
    }
}
