<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    protected $fillable = [
        'tanggal',
        'awal',
        'akhir',
        'selisih',
        'harga',
        'pakai',
        'total',
        'jumlah',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'awal' => 'decimal:2',
        'akhir' => 'decimal:2',
        'selisih' => 'decimal:2',
        'harga' => 'decimal:2',
        'pakai' => 'decimal:2',
        'total' => 'decimal:2',
        'jumlah' => 'decimal:2',
    ];

    // Biar otomatis hitung pas create
    protected static function booted()
    {
        static::creating(function ($check) {
            // 1. Hitung selisih
            $check->selisih = $check->akhir - $check->awal;
            
            // 2. Hitung pakai (selisih * harga)
            $check->pakai = $check->selisih * $check->harga;

            // 3. Hitung total & jumlah kumulatif
            $kemarin = self::orderBy('tanggal', 'desc')->first();

            if ($kemarin) {
                $check->total = $kemarin->total + $check->selisih;
                $check->jumlah = $kemarin->jumlah + $check->pakai;
            } else {
                // Kalau data pertama
                $check->total = $check->selisih;
                $check->jumlah = $check->pakai;
            }
        });
    }
}

