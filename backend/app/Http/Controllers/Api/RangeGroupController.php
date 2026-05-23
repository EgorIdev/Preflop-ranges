<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RangeGroup;

use Illuminate\Http\Request;

class RangeGroupController extends Controller
{
    public function index()
    {
        return RangeGroup::with('ranges')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
        ]);

        $group = RangeGroup::create([
            'name' => $validated['name'],
        ]);

        return response()->json($group);
    }
}