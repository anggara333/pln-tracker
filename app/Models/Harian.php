<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Harian extends Model
{
    protected $table = 'harians';

    protected $fillable = [
        'tanggal',
        'uraian',
        'kredit',
        'debit',
        'saldo',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'kredit' => 'decimal:2',
        'debit' => 'decimal:2',
        'saldo' => 'decimal:2',
    ];
}
