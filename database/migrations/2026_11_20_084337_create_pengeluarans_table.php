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
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal_pengeluaran');
            $table->string('kategori', 80);
            $table->string('keterangan', 255);
            $table->unsignedBigInteger('jumlah');
            $table->string('metode_pembayaran', 20);
            $table->timestamps();

            $table->index(['user_id', 'tanggal_pengeluaran']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran');
    }
};
