<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\History;

class HistoryController extends Controller
{
    public function index()
    {
        $histories = History::orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $histories,
        ]);
    }
}