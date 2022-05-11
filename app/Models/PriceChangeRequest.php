<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceChangeRequest extends Model
{
    use HasFactory;
    protected $guarded = [];
    /**
     * relationships
     */
    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }
}
