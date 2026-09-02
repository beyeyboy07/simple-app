<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Terbilang;
use Illuminate\Http\Request;
use App\Models\History;

class TerbilangController extends Controller
{
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'nominal' => [
                'required',
                'integer',
                'min:0',
                'max:999999999999',
            ],
        ]);

        $nominal = (int) $validated['nominal'];

        $hasil = $this->convertToWords($nominal);

        if ($nominal === 0) {
            $hasil = 'nol rupiah';
        } else {
            $hasil .= ' rupiah';
        }

        $terbilang = Terbilang::create([
            'user_id' => $request->user()->id,
            'nominal' => $nominal,
            'hasil' => $hasil,
        ]);


        History::create([
            'user_id' => $request->user()->id,
            'type' => 'terbilang',
            'action' => 'generate',
            'description' => 'User melakukan konversi nominal ke terbilang',
            'data' => [
                'nominal' => $nominal,
                'hasil' => $hasil,
                'terbilang_id' => $terbilang->id,
            ],
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nominal berhasil dikonversi.',
            'data' => [
                'id' => $terbilang->id,
                'nominal' => $terbilang->nominal,
                'hasil' => $terbilang->hasil,
                'created_at' => $terbilang->created_at,
            ],
        ], 201);
    }

   private function convertToWords(int $number): string
    {
        $words = [
            '',
            'satu',
            'dua',
            'tiga',
            'empat',
            'lima',
            'enam',
            'tujuh',
            'delapan',
            'sembilan',
            'sepuluh',
            'sebelas',
        ];

        if ($number < 12) {
            return $words[$number];
        }

        if ($number < 20) {
            return trim(
                $this->convertToWords($number - 10)
                . ' belas'
            );
        }

        if ($number < 100) {
            return trim(
                $this->convertToWords(
                    intdiv($number, 10)
                )
                . ' puluh '
                . $this->convertToWords($number % 10)
            );
        }

        if ($number < 200) {
            return trim(
                'seratus '
                . $this->convertToWords($number - 100)
            );
        }

        if ($number < 1000) {
            return trim(
                $this->convertToWords(
                    intdiv($number, 100)
                )
                . ' ratus '
                . $this->convertToWords($number % 100)
            );
        }

        if ($number < 2000) {
            return trim(
                'seribu '
                . $this->convertToWords($number - 1000)
            );
        }

        if ($number < 1000000) {
            return trim(
                $this->convertToWords(
                    intdiv($number, 1000)
                )
                . ' ribu '
                . $this->convertToWords($number % 1000)
            );
        }

        if ($number < 1000000000) {
            return trim(
                $this->convertToWords(
                    intdiv($number, 1000000)
                )
                . ' juta '
                . $this->convertToWords($number % 1000000)
            );
        }

        if ($number < 1000000000000) {
            return trim(
                $this->convertToWords(
                    intdiv($number, 1000000000)
                )
                . ' miliar '
                . $this->convertToWords($number % 1000000000)
            );
        }

        return '';
    }

    public function history(Request $request)
    {
        $history = Terbilang::where(
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