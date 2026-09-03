<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InputData;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\History;

class InputDataController extends Controller
{
    /**
     * Menampilkan seluruh data user.
     */
    public function index()
    {
        $data = InputData::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Menyimpan data user baru.
     */
    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $this->validateSalaryRange($validated);

        $data = InputData::create($validated);

        History::create([
            'user_id' => $request->user()->id,
            'type' => 'input_data',
            'action' => 'create',
            'description' => 'User membuat data input',
            'data' => [
                ...$validated,
                'input_data_id' => $data->id,
            ],
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan.',
            'data' => $data,
        ], 201);
    }

    /**
     * Mengupdate data user.
     */
    public function update(Request $request, $id)
    {
        $data = InputData::find($id);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Data user tidak ditemukan.',
            ], 404);
        }

        $validated = $this->validateData($request);

        $this->validateSalaryRange($validated);

        $data->update($validated);

        History::create([
            'user_id' => $request->user()->id,
            'type' => 'input_data',
            'action' => 'update',
            'description' => 'User mengubah data input',
            'data' => [
                ...$validated,
                'input_data_id' => $data->id,
            ],
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui.',
            'data' => $data->fresh(),
        ]);
    }

    /**
     * Menghapus data user.
     */
    public function destroy(Request $request, $id)
    {
        $data = InputData::find($id);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Data user tidak ditemukan.',
            ], 404);
        }

        $deletedData = [
            'nama' => $data->nama,
            'email' => $data->email,
            'no_hp' => $data->no_hp,
            'alamat' => $data->alamat,
            'provinsi' => $data->provinsi,
            'kota' => $data->kota,
            'kecamatan' => $data->kecamatan,
            'kelurahan' => $data->kelurahan,
            'level' => $data->level,
            'range_gaji' => $data->range_gaji,
            'gaji' => $data->gaji,
            'input_data_id' => $data->id,
        ];

        $data->delete();

        History::create([
            'user_id' => $request->user()->id,
            'type' => 'input_data',
            'action' => 'delete',
            'description' => 'User menghapus data input',
            'data' => $deletedData,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus.',
        ]);
    }

    /**
     * Validasi data input.
     */
    private function validateData(Request $request)
    {
        return $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\s]+$/u',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
            ],

            'no_hp' => [
                'required',
                'string',
                'regex:/^62[1-9][0-9]{8,12}$/',
                'max:20',
            ],

            'alamat' => [
                'required',
                'string',
            ],

            'provinsi' => [
                'required',
                'string',
                'max:255',
            ],

            'kota' => [
                'required',
                'string',
                'max:255',
            ],

            'kecamatan' => [
                'required',
                'string',
                'max:255',
            ],

            'kelurahan' => [
                'required',
                'string',
                'max:255',
            ],

            'level' => [
                'required',
                'string',
                'max:100',
            ],

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

            'gaji' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);
    }

    /**
     * Validasi gaji terhadap range yang dipilih.
     */
    private function validateSalaryRange(array $validated)
    {
        $range = $validated['range_gaji'];
        $gaji = $validated['gaji'];

        $validRange = match ($range) {
            '1 - 3 juta' =>
                $gaji >= 1000000 && $gaji <= 3000000,

            '3 - 5 juta' =>
                $gaji >= 3000000 && $gaji <= 5000000,

            '5 - 10 juta' =>
                $gaji >= 5000000 && $gaji <= 10000000,

            '10 - 15 juta' =>
                $gaji >= 10000000 && $gaji <= 15000000,

            '15 - 20 juta' =>
                $gaji >= 15000000 && $gaji <= 20000000,

            default => false,
        };

        if (!$validRange) {
            abort(response()->json([
                'success' => false,
                'message' =>
                    'Gaji tidak sesuai dengan range gaji yang dipilih.',
            ], 422));
        }
    }
}