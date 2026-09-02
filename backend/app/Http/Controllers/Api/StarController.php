<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StarGeneration;
use Illuminate\Http\Request;
use App\Models\History;

class StarController extends Controller
{
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'jumlah' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],
            'tipe' => [
                'required',
                'integer',
                'in:1,2,3',
            ],
        ]);

        $jumlah = $validated['jumlah'];
        $tipe = $validated['tipe'];

        $hasil = $this->generatePattern(
            $jumlah,
            $tipe
        );

        $star = StarGeneration::create([
            'user_id' => $request->user()->id,
            'jumlah' => $jumlah,
            'tipe' => $tipe,
            'hasil' => $hasil,
        ]);

        History::create([
            'user_id' => $request->user()->id,
            'type' => 'bintang',
            'action' => 'generate',
            'description' => 'User membuat generate bintang',
            'data' => [
                'jumlah' => $jumlah,
                'tipe' => $tipe,
                'hasil' => $hasil,
                'star_id' => $star->id,
            ],
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bintang berhasil digenerate.',
            'data' => [
                'id' => $star->id,
                'jumlah' => $star->jumlah,
                'tipe' => $star->tipe,
                'hasil' => $star->hasil,
                'created_at' => $star->created_at,
            ],
        ], 201);
    }

    private function generatePattern(
        int $jumlah,
        int $tipe
    ): string {
        $rows = [];

        for ($i = 1; $i <= $jumlah; $i++) {

            switch ($tipe) {

                // Segitiga siku kanan bawah
                case 1:
                    $rows[] = str_repeat(
                        ' ',
                        $jumlah - $i
                    ) . str_repeat('*', $i);

                    break;

                // Segitiga biasa
                case 2:
                    $rows[] = str_repeat(
                        ' ',
                        $jumlah - $i
                    ) . str_repeat('*', (2 * $i) - 1);

                    break;

                // Segitiga siku kiri bawah
               case 3:
                    $rows[] = str_repeat('*', $i);

                    break;
            }
        }

        return implode("\n", $rows);
    }

    public function history(Request $request)
    {
        $history = StarGeneration::where(
            'user_id',
            $request->user()->id
        )
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $history,
        ]);
    }
}