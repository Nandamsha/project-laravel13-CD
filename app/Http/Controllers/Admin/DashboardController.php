<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stand;
use App\Models\User;
use App\Notifications\StandApproved;
use App\Notifications\StandRejected;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $waiting = User::with('stand')
            ->where('role', 'seller')
            ->where('status', 'pending')
            ->oldest()
            ->get();

        $stands = Stand::with('user')
            ->withCount('products')
            ->whereHas('user', fn ($q) => $q->where('status', '!=', 'pending'))
            ->orderBy('name')
            ->get();

        return view('admin.dashboard', [
            'waiting' => $waiting,
            'stands' => $stands,
            'productCount' => \App\Models\Product::count(),
        ]);
    }

    public function approve(User $user): RedirectResponse
    {
        abort_unless($user->role === 'seller' && $user->stand, 404);

        $user->update(['status' => 'approved']);
        $user->stand->update(['is_active' => true]);

        return $this->notifyAndBack($user, new StandApproved($user->stand), 'Stand "'.$user->stand->name.'" diterima.');
    }

    public function reject(User $user): RedirectResponse
    {
        abort_unless($user->role === 'seller' && $user->stand, 404);

        $user->update(['status' => 'rejected']);
        $user->stand->update(['is_active' => false]);

        return $this->notifyAndBack($user, new StandRejected($user->stand), 'Pengajuan "'.$user->stand->name.'" ditolak.');
    }

    public function toggle(Stand $stand): RedirectResponse
    {
        $stand->update(['is_active' => ! $stand->is_active]);

        return back()->with('status', 'Stand "'.$stand->name.'" '.($stand->is_active ? 'diaktifkan.' : 'dinonaktifkan.'));
    }

    public function destroy(Stand $stand): RedirectResponse
    {
        if ($stand->photo) {
            Storage::disk('public')->delete($stand->photo);
        }
        foreach ($stand->products as $product) {
            if ($product->photo) {
                Storage::disk('public')->delete($product->photo);
            }
        }

        $name = $stand->name;
        $stand->user->delete(); // stand dan menu ikut terhapus (cascade)

        return back()->with('status', 'Stand "'.$name.'" beserta seluruh menunya berhasil dihapus.');
    }

    private function notifyAndBack(User $user, $notification, string $message): RedirectResponse
    {
        try {
            $user->notify($notification);
            $message .= ' Pemberitahuan dikirim ke '.$user->email.'.';
        } catch (\Throwable $e) {
            report($e);
            $message .= ' Pemberitahuan email gagal dikirim, periksa pengaturan MAIL di .env.';
        }

        return back()->with('status', $message);
    }
}
