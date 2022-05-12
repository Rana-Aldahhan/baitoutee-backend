<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserJoinRequest extends Model
{
    use HasFactory;
    /**
     * The attributes that are not mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];
     /**
     * relationships
     */
    public function location()
    {
        $this->belongsTo(Location::class,'location_id');
    }
    public function user()
    {
        $this->belongsTo(User::class,'user_id');
    }

}
