<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Buku extends Model
{
    protected $fillable =[
        'judul',
        'pengarang',
        'kategori',
    ];

    public function penjualans(): HasMany
    {
        return $this->hasMany(Penjualan::class, 'buku_id');
    }
    public function getTerjualAttribute(): int
    {
        // Mengakses relasi 'penjualans' dan menjumlahkan kolom 'eksemplar'
        return $this->penjualans->sum('eksemplar');
    }
}
