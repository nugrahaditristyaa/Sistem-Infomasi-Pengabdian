<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        try {

            if (Auth::guard('admin')->attempt($credentials)) {
                $user = Auth::guard('admin')->user();
                if (strtolower($user->role) === 'staff inqa') {
                    return redirect()->route('inqa.kpi.index')->with('success', 'Login Berhasil');
                }
                return redirect()->route('admin.dashboard')->with('success', 'Login Berhasil');
            }

            return back()->with('error', 'Email atau Password salah.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat login.');
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
