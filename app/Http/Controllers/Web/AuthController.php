<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        // If user is already logged in, redirect to dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')
            ->with('success', 'Anda berhasil keluar dari sistem.');
    }

    public function createDefaultAdmin()
    {
        // Check if admin user already exists
        $adminExists = User::where('email', 'admin@wablast.com')->exists();
        
        if ($adminExists) {
            return response()->json([
                'success' => false,
                'message' => 'Admin user sudah ada'
            ]);
        }

        // Create default admin user
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@wablast.com',
            'password' => Hash::make('admin123'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Admin user berhasil dibuat',
            'data' => [
                'email' => $admin->email,
                'password' => 'admin123'
            ]
        ]);
    }
} 