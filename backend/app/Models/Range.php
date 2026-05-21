<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\RangeItem;

class Range extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'spot',
    ];

    public function items()
    {
        return $this->hasMany(RangeItem::class);
    }
}