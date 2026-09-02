<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InputData;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InputDataController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'no_hp' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string'],

            'provinsi' => ['required', 'string', 'max:255'],
            'kota' => ['required', 'string', 'max:255'],
            'kecamatan' => ['required', 'string', 'max:255'],
            'kelurahan' => ['required', 'string', 'max:255'],

            'level' => ['required', 'string', 'max:100'],
            'range_gaji' => [
                'required',
                Rule::in([
                    '1 - 3 juta',
                    '3 - 5 juta',
                    '5 - 10 juta',
                    '10 - 15 juta',
                    '15 - 20 juta',
                ]),
            ],
            'gaji' => ['required', 'integer', 'min:1'],
        ]);

        $range = $validated['range_gaji'];
        $gaji = $validated['gaji'];

        $validRange = match ($range) {
            '1 - 3 juta' => $gaji >= 1000000 && $gaji <= 3000000,
            '3 - 5 juta' => $gaji >= 3000000 && $gaji <= 5000000,
            '5 - 10 juta' => $gaji >= 5000000 && $gaji <= 10000000,
            '10 - 15 juta' => $gaji >= 10000000 && $gaji <= 15000000,
            '15 - 20 juta' => $gaji >= 15000000 && $gaji <= 20000000,
            default => false,
        };

        if (!$validRange) {
            return response()->json([
                'success' => false,
                'message' => 'Gaji tidak sesuai dengan range gaji yang dipilih.',
            ], 422);
        }

        $data = InputData::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan.',
            'data' => $data,
        ], 201);
    }

    public function index()
    {
        $data = InputData::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}