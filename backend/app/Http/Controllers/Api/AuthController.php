<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
   public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'captcha_id' => ['required', 'uuid'],
            'captcha_code' => ['required', 'string', 'size:6'],
        ]);

        $captchaKey = 'captcha:' . $credentials['captcha_id'];

        $captchaHash = Cache::pull($captchaKey);

        if (!$captchaHash) {
            return response()->json([
                'success' => false,
                'message' => 'CAPTCHA sudah expired atau tidak valid',
            ], 422);
        }

        $inputCaptchaHash = hash(
            'sha256',
            strtoupper($credentials['captcha_code'])
        );

        if (!hash_equals($captchaHash, $inputCaptchaHash)) {
            return response()->json([
                'success' => false,
                'message' => 'CAPTCHA tidak valid',
            ], 422);
        }

        $user = User::where(
            'email',
            $credentials['email']
        )->first();

        if (!$user || !Hash::check(
            $credentials['password'],
            $user->password
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        $token = $user
            ->createToken('web-token')
            ->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }
}