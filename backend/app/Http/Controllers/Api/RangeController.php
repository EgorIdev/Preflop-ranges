<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRangeRequest;
use App\Models\Range;

use App\Models\RangeItem;
use Illuminate\Support\Str;

use Illuminate\Http\Request;

class RangeController extends Controller
{
    public function index()
    {
        return Range::with('items')->get();
    }

    public function store(StoreRangeRequest $request)
    {
        $range = Range::create([
            'name' => $request->name,
            'group_id' => $request->group_id,
        ]);

    return response()->json($range);
    }

    public function destroy(Range $range)
    {
        $range->items()->delete();

        $range->delete();

        return response()->json([
            'message' => 'Range deleted'
        ]);
    }

    public function copy(Range $range)
    {
        $copyCount = Range::where(
            'name',
            'like',
            $range->name . ' - copy%'
        )->count();

        $newRange = Range::create([
            'name' =>
                $range->name
                . ' - copy('
                . ($copyCount + 1)
                . ')',

            'group_id' =>
                $range->group_id,
        ]);

        foreach ($range->items as $item) {

            RangeItem::create([

                'range_id' =>
                    $newRange->id,

                'hand' =>
                    $item->hand,

                'raise_percentage' =>
                    $item->raise_percentage,

                'call_percentage' =>
                    $item->call_percentage,

                'fold_percentage' =>
                    $item->fold_percentage,
            ]);
        }

        return response()->json(
            $newRange->load('items')
        );
    }    

    public function saveItems(
        Request $request,
        Range $range
    )
    {
        $validated = $request->validate([
            'items' => ['required', 'array'],
        ]);

        $range->items()->delete();

        foreach ($validated['items'] as $item) {

            RangeItem::create([
                'range_id' => $range->id,

                'hand' => $item['hand'],

                'raise_percentage' =>
                    $item['raise_percentage'] ?? 0,

                'call_percentage' =>
                    $item['call_percentage'] ?? 0,

                'fold_percentage' =>
                    $item['fold_percentage'] ?? 0,
            ]);
        }

        return response()->json([
            'message' => 'Range items saved',
        ]);
    }  
    
    
}
