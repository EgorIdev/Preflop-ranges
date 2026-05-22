<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRangeRequest;
use App\Models\Range;

use App\Models\RangeItem;

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
                'action' => $item['action'],
            ]);
        }

        return response()->json([
            'message' => 'Range items saved',
        ]);
    }    
    
    
}
