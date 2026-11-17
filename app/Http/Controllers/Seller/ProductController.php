<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'price' => ['required', 'integer', 'min:0', 'max:10000000'],
            'description' => ['nullable', 'string', 'max:200'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'is_available' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama menu wajib diisi.',
            'price.required' => 'Harga menu wajib diisi.',
            'price.integer' => 'Harga harus berupa angka bulat.',
            'photo.image' => 'Foto menu harus berupa gambar.',
            'photo.max' => 'Ukuran foto menu maksimal 2 MB.',
        ]);

        $data['is_available'] = $request->boolean('is_available', true);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('products', 'public');
        }

        $request->user()->stand->products()->create($data);

        return back()->with('status', 'Menu "'.$data['name'].'" berhasil ditambahkan.');
    }

    // Produk selalu dicari lewat stand milik user yang login, sehingga penjual
    // tidak bisa mengubah menu stand lain.
    public function update(Request $request, int $product): RedirectResponse
    {
        $item = $request->user()->stand->products()->findOrFail($product);

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'price' => ['required', 'integer', 'min:0', 'max:10000000'],
            'description' => ['nullable', 'string', 'max:200'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'is_available' => ['required', 'boolean'],
        ], [
            'name.required' => 'Nama menu wajib diisi.',
            'price.required' => 'Harga menu wajib diisi.',
            'price.integer' => 'Harga harus berupa angka bulat.',
            'photo.image' => 'Foto menu harus berupa gambar.',
            'photo.max' => 'Ukuran foto menu maksimal 2 MB.',
        ]);

        if ($request->hasFile('photo')) {
            if ($item->photo) {
                Storage::disk('public')->delete($item->photo);
            }
            $data['photo'] = $request->file('photo')->store('products', 'public');
        } else {
            unset($data['photo']);
        }

        if (! $request->exists('description')) {
            unset($data['description']);
        }

        $item->update($data);

        return back()->with('status', 'Menu "'.$item->name.'" berhasil diperbarui.');
    }

    public function destroy(Request $request, int $product): RedirectResponse
    {
        $item = $request->user()->stand->products()->findOrFail($product);

        if ($item->photo) {
            Storage::disk('public')->delete($item->photo);
        }
        $item->delete();

        return back()->with('status', 'Menu "'.$item->name.'" berhasil dihapus.');
    }
}
