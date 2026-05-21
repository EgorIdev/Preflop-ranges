<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRangeRequest;
use App\Models\Range;

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
            'spot' => $request->spot,
        ]);

        return response()->json([
            'message' => 'Range created successfully',
            'data' => $range,
        ], 201);
    }
}
