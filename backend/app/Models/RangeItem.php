<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Range;

class RangeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'range_id',
        'hand',
        'action',
    ];

    public function range()
    {
        return $this->belongsTo(Range::class);
    }
}
