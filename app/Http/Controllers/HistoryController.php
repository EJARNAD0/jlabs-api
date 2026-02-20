<?php

namespace App\Http\Controllers;

use App\Models\IpHistory;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        return IpHistory::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ip' => ['required', 'string'],
            'payload' => ['nullable', 'array'],
        ]);

        $history = IpHistory::create([
            'user_id' => $request->user()->id,
            'ip' => $data['ip'],
            'payload' => $data['payload'] ?? null,
        ]);

        return response()->json($history, 201);
    }

    public function bulkDelete(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        IpHistory::query()
            ->where('user_id', $request->user()->id)
            ->whereIn('id', $data['ids'])
            ->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
