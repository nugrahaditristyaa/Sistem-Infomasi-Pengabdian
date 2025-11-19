<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // NOTE: Login throttling removed as requested.

        // Validasi input
        $credentials = $request->validate([
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-zA-Z0-9._-]+$/'
            ],
            'password' => [
                'required',
                'string',
                'min:6'
            ],
        ], [
            'username.required' => 'Username wajib diisi',
            'username.min' => 'Username minimal 3 karakter',
            'username.max' => 'Username maksimal 50 karakter',
            'username.regex' => 'Username hanya boleh mengandung huruf, angka, titik, underscore, dan dash',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
        ]);

        try {
            // Cek apakah username ada di database
            $user = User::where('username', $credentials['username'])->first();

            if (!$user) {
                return back()
                    ->withInput($request->only('username'))
                    ->with('login_error', 'Username "' . $credentials['username'] . '" tidak ditemukan. Silakan periksa kembali username Anda atau hubungi administrator.');
            }

            // Cek apakah password benar
            if (!Hash::check($credentials['password'], $user->password)) {
                return back()
                    ->withInput($request->only('username'))
                    ->with('login_error', 'Password yang Anda masukkan salah. Silakan coba lagi.');
            }

            // Attempt login dengan guard admin
            if (Auth::guard('admin')->attempt($credentials)) {
                $user = Auth::guard('admin')->user();

                // Regenerate session untuk keamanan
                $request->session()->regenerate();

                // Log successful login
                Log::info('Successful login', [
                    'username' => $user->username,
                    'name' => $user->name,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);

                // Redirect berdasarkan role
                if (strtolower($user->role) === 'dekan') {
                    return redirect()->route('dekan.dashboard')
                        ->with('success', 'Selamat datang, ' . $user->name . '! Login berhasil.');
                } elseif ($user->role === 'Kaprodi TI') {
                    return redirect()->route('kaprodi.ti.dashboard')
                        ->with('success', 'Selamat datang, ' . $user->name . '! Login berhasil.');
                } elseif ($user->role === 'Kaprodi SI') {
                    return redirect()->route('kaprodi.si.dashboard')
                        ->with('success', 'Selamat datang, ' . $user->name . '! Login berhasil.');
                }

                return redirect()->route('admin.dashboard')
                    ->with('success', 'Selamat datang, ' . $user->name . '! Login berhasil.');
            }

            // Jika attempt gagal (kondisi edge case)
            return back()
                ->withInput($request->only('username'))
                ->with('login_error', 'Terjadi kesalahan saat melakukan login. Silakan coba lagi.');
        } catch (\Exception $e) {
            // Log error untuk debugging
            Log::error('Login error: ' . $e->getMessage(), [
                'username' => $credentials['username'] ?? null,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return back()
                ->withInput($request->only('username'))
                ->with('login_error', 'Terjadi kesalahan sistem. Silakan coba lagi dalam beberapa saat atau hubungi administrator.');
        }
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login')->with('success', 'Anda telah logout');
    }
}
