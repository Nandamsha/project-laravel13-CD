<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Models\Stand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stand = $request->user()->stand()->with(['products' => fn ($q) => $q->latest()])->firstOrFail();

        return view('seller.dashboard', [
            'stand' => $stand,
            'categories' => array_keys(Stand::CATEGORIES),
            'notices' => $request->user()->unreadNotifications,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $stand = $request->user()->stand()->firstOrFail();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', Rule::in(array_keys(Stand::CATEGORIES))],
            'location' => ['required', 'string', 'max:150'],
            'whatsapp' => ['required', 'regex:/^[0-9+\s-]{9,20}$/'],
            'description' => ['nullable', 'string', 'max:500'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ], [
            'name.required' => 'Nama stand wajib diisi.',
            'location.required' => 'Lokasi stand wajib diisi.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.regex' => 'Nomor WhatsApp tidak valid.',
            'photo.image' => 'File foto stand harus berupa gambar.',
            'photo.max' => 'Ukuran foto stand maksimal 2 MB.',
        ]);

        $data['whatsapp'] = AuthController::normalizeWhatsapp($data['whatsapp']);

        if ($request->hasFile('photo')) {
            if ($stand->photo) {
                Storage::disk('public')->delete($stand->photo);
            }
            $data['photo'] = $request->file('photo')->store('stands', 'public');
        } else {
            unset($data['photo']);
        }

        $stand->update($data);

        return back()->with('status', 'Info stand berhasil disimpan.');
    }

    public function readNotices(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}
