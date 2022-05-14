<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;
    /**
     * relationships
     */
    public function orders(){
        return $this->hasMany(Order::class);
    }
    public function deliveryman(){
        return $this->belongesTo(Deliveryman::class,'deliveryman_id');
    }
}
