<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = ['stand_id', 'name', 'price', 'description', 'photo', 'is_available'];

    protected function casts(): array
    {
        return ['is_available' => 'boolean', 'price' => 'integer'];
    }

    public function stand(): BelongsTo
    {
        return $this->belongsTo(Stand::class);
    }

    public function imageUrl(): string
    {
        return $this->photo
            ? asset('storage/'.$this->photo)
            : $this->stand->imageUrl();
    }
}
