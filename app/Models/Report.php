<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    public function sendable()
    {
        return $this->morphTo()->withTrashed();;
    }
    public function receivable()
    {
        return $this->morphTo()->withTrashed();;
    }
    public function order(){
        return $this->belongsTo(Order::class);
    }
}
