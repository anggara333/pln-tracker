<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('checks', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique(); // biar 1 hari 1 data
            $table->decimal('awal', 10, 2); // meter awal
            $table->decimal('akhir', 10, 2); // meter akhir
            $table->decimal('selisih', 10, 2); // akhir - awal
            $table->decimal('harga', 10, 2)->default(1487.20); // harga per kwh
            $table->decimal('pakai', 12, 2); // selisih * harga
            $table->decimal('total', 12, 2); // total = selisih kemarin + selisih hari ini (kumulatif kwh)
            $table->decimal('jumlah', 14, 2); // jumlah = pakai kemarin + pakai hari ini (kumulatif rupiah)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checks');
    }
};

