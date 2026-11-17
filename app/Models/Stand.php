<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stand extends Model
{
    /** Kategori => gambar bawaan template (dipakai bila stand belum punya foto). */
    public const CATEGORIES = [
        'Makanan Berat' => 'menu/4.jpg',
        'Camilan' => 'off-img.jpg',
        'Minuman' => 'menu/3.jpg',
        'Dessert' => 'menu/5.jpg',
        'Lainnya' => 'menu/2.jpg',
    ];

    protected $fillable = ['user_id', 'name', 'category', 'location', 'whatsapp', 'description', 'photo', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function imageUrl(): string
    {
        return $this->photo
            ? asset('storage/'.$this->photo)
            : asset('assets/img/'.(self::CATEGORIES[$this->category] ?? 'menu/1.jpg'));
    }
}
