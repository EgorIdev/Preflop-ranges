<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\RangeGroup;

class RangeGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function ranges()
    {
        return $this->hasMany(
            Range::class,
            'group_id'
        );
    }

    public function group()
    {
        return $this->belongsTo(
            RangeGroup::class
        );
    }
}