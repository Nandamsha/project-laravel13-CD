<?php

namespace App\Http\Controllers;

use App\Models\Stand;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'Email atau kata sandi salah.'])->onlyInput('email');
        }

        // Pemilik stand baru hanya bisa masuk setelah pengajuannya diterima admin.
        if ($user->status === 'pending') {
            return back()->withErrors(['email' => 'Pengajuan stand Anda masih dalam daftar tunggu. Anda bisa masuk setelah admin menerimanya.'])->onlyInput('email');
        }
        if ($user->status === 'rejected') {
            return back()->withErrors(['email' => 'Pengajuan stand Anda belum dapat diterima admin.'])->onlyInput('email');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->route($user->isAdmin() ? 'admin.dashboard' : 'seller.dashboard');
    }

    public function showRegister()
    {
        return view('auth.register', ['categories' => array_keys(Stand::CATEGORIES)]);
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'stand_name' => ['required', 'string', 'max:100'],
            'category' => ['required', Rule::in(array_keys(Stand::CATEGORIES))],
            'location' => ['required', 'string', 'max:150'],
            'whatsapp' => ['required', 'regex:/^[0-9+\s-]{9,20}$/'],
            'description' => ['nullable', 'string', 'max:500'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama pemilik wajib diisi.',
            'stand_name.required' => 'Nama stand wajib diisi.',
            'location.required' => 'Lokasi stand wajib diisi.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.regex' => 'Nomor WhatsApp tidak valid.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ]);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'seller',
                'status' => 'pending',
            ]);

            $user->stand()->create([
                'name' => $data['stand_name'],
                'category' => $data['category'],
                'location' => $data['location'],
                'whatsapp' => $this->normalizeWhatsapp($data['whatsapp']),
                'description' => $data['description'] ?? null,
                'is_active' => false,
            ]);
        });

        return redirect()->route('register.done')->with('submitted', [
            'stand' => $data['stand_name'],
            'email' => $data['email'],
        ]);
    }

    public function done(Request $request)
    {
        abort_unless($request->session()->has('submitted'), 404);

        return view('auth.pending', ['submitted' => $request->session()->get('submitted')]);
    }

    public function updateEmail(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => ['required', 'current_password'],
        ], [
            'email.required' => 'Email baru wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 150 karakter.',
            'email.unique' => 'Email ini sudah digunakan akun lain.',
            'current_password.required' => 'Kata sandi saat ini wajib diisi untuk konfirmasi.',
            'current_password.current_password' => 'Kata sandi saat ini salah.',
        ]);

        if ($data['email'] === $user->email) {
            return back()->with('status', 'Email tidak berubah karena sama dengan email saat ini.');
        }

        $user->update(['email' => $data['email']]);

        return back()->with('status', 'Email akun berhasil diperbarui menjadi '.$data['email'].'.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /** 0812..., 812..., atau +62812... menjadi 62812... agar cocok dengan tautan wa.me */
    public static function normalizeWhatsapp(string $number): string
    {
        $digits = preg_replace('/\D/', '', $number);

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }
        if (str_starts_with($digits, '8')) {
            return '62'.$digits;
        }

        return $digits;
    }
}
