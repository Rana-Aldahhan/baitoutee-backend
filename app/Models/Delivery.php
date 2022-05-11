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
        $this->hasMany(Order::class);
    }
    public function deliveryman(){
        $this->belongesTo(Deliveryman::class,'deliveryman_id');
    }
}
