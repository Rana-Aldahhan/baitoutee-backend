<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
     /**
     * relationships
     */
    public function users(){
        $this->hasMany(User::class);
    }
}
