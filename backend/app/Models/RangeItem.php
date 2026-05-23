<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Range;

class RangeItem extends Model
{
    protected $fillable = [
        'range_id',
        'hand',
        'raise_percentage',
        'call_percentage',
        'fold_percentage',
    ];

    public function range()
    {
        return $this->belongsTo(Range::class);
    }
}
