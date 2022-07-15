<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChefJoinRequest extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    protected $guarded = [];
    /**
     * relationships
     */
    public function chef()
    {
        return $this->hasOne(Chef::class);
    }
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
