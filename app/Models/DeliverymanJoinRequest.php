<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliverymanJoinRequest extends Model
{
    use HasFactory;
    protected $guarded = [];
    /**
     * relationships
     */
    public function deliveryman()
    {
        return $this->hasOne(Deliveryman::class);
    }
}
