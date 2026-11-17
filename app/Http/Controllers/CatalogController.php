<?php

namespace App\Http\Controllers;

use App\Models\Stand;

class CatalogController extends Controller
{
    /** Halaman publik: beranda, stand, menu, cara pesan. Tanpa login. */
    public function index()
    {
        $stands = Stand::with('products')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn (Stand $s) => [
                'id' => $s->id,
                'name' => $s->name,
                'cat' => $s->category,
                'loc' => $s->location,
                'wa' => $s->whatsapp,
                'desc' => (string) $s->description,
                'img' => $s->imageUrl(),
                'products' => $s->products->map(fn ($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => $p->price,
                    'desc' => (string) $p->description,
                    'ok' => $p->is_available,
                    'img' => $p->imageUrl(),
                ])->values(),
            ])->values();

        return view('home', ['data' => [
            'stands' => $stands,
            'categories' => array_keys(Stand::CATEGORIES),
            'assets' => asset('assets/img').'/',
        ]]);
    }
}
