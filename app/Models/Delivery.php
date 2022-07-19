<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    protected $guarded = [];
    protected $dates = ['delivered_at'];
    protected $casts = [
        'paid_to_deliveryman'=>'boolean'
    ];
    /**
     * relationships
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function deliveryman()
    {
        return $this->belongsTo(Deliveryman::class, 'deliveryman_id');
    }
}
