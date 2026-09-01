<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CaptchaController extends Controller
{
    public function generate()
    {
        $captchaId = Str::uuid()->toString();

        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        $code = '';

        for ($i = 0; $i < 6; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }

        // Simpan hash CAPTCHA selama 5 menit
        Cache::put(
            'captcha:' . $captchaId,
            hash('sha256', strtoupper($code)),
            now()->addMinutes(5)
        );

        // Ukuran gambar
        $width = 180;
        $height = 60;

        $image = imagecreatetruecolor($width, $height);

        // Background
        $background = imagecolorallocate(
            $image,
            245,
            245,
            245
        );

        imagefill($image, 0, 0, $background);

        // Warna teks
        $textColor = imagecolorallocate(
            $image,
            30,
            30,
            30
        );

        // Noise
        for ($i = 0; $i < 100; $i++) {
            $noiseColor = imagecolorallocate(
                $image,
                random_int(100, 220),
                random_int(100, 220),
                random_int(100, 220)
            );

            imagesetpixel(
                $image,
                random_int(0, $width - 1),
                random_int(0, $height - 1),
                $noiseColor
            );
        }

        // Garis random
        for ($i = 0; $i < 5; $i++) {
            $lineColor = imagecolorallocate(
                $image,
                random_int(100, 200),
                random_int(100, 200),
                random_int(100, 200)
            );

            imageline(
                $image,
                random_int(0, $width),
                random_int(0, $height),
                random_int(0, $width),
                random_int(0, $height),
                $lineColor
            );
        }

        // Tulis CAPTCHA
        $font = 5;

        imagestring(
            $image,
            $font,
            35,
            22,
            $code,
            $textColor
        );

        // Convert PNG menjadi Base64
        ob_start();

        imagepng($image);

        $imageData = ob_get_clean();

        imagedestroy($image);

        return response()->json([
            'success' => true,
            'captcha_id' => $captchaId,
            'image' => 'data:image/png;base64,' . base64_encode($imageData),
        ]);
    }
}