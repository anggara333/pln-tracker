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
        Schema::create('harians', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('uraian');
            $table->decimal('kredit', 15, 2)->default(0);
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('saldo', 15, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harians');
    }
};
