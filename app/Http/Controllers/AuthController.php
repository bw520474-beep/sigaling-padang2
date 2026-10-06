<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            return $this->redirectBasedOnRole($user);
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            return $this->redirectBasedOnRole($user);
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'no_hp' => ['nullable', 'string', 'max:15'],
            'password' => ['required', 'confirmed', 'min:6'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email sudah terdaftar di sistem.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
        ]);

        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'masyarakat',
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Selamat datang! Akun Anda berhasil didaftarkan.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }

    public function profil()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $laporans = $user ? $user->laporans()->with('kategori')->orderBy('created_at', 'desc')->get() : collect();

        return view('masyarakat.profil', compact('user', 'laporans'));
    }

    public function updateProfil(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:15'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
        ]);

        $user->nama = $validated['nama'];
        $user->no_hp = $validated['no_hp'] ?? null;
        $user->save();

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:6'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini tidak cocok.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password baru minimal 6 karakter.',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password Anda berhasil diubah.');
    }

    /**
     * @param \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable|null $user
     */
    private function redirectBasedOnRole($user)
    {
        if ($user) {
            if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ((method_exists($user, 'isAdminInstansi') && $user->isAdminInstansi()) || (method_exists($user, 'isSupervisor') && $user->isSupervisor())) {
                return redirect()->route('instansi.dashboard');
            } elseif (method_exists($user, 'isPetugas') && $user->isPetugas()) {
                return redirect()->route('petugas.dashboard');
            } elseif (method_exists($user, 'isAuditor') && $user->isAuditor()) {
                return redirect()->route('auditor.dashboard');
            }
        }

        return redirect()->route('home');
    }
}
