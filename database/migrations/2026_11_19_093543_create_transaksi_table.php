
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();

            // Karyawan yang memproses transaksi
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('kode_transaksi', 40)
                ->unique();

            $table->dateTime('tanggal_transaksi');

            $table->string('metode_pembayaran', 20);

            $table->unsignedBigInteger('total');

            // Digunakan terutama untuk pembayaran tunai
            $table->unsignedBigInteger('nominal_diterima')
                ->nullable();

            $table->unsignedBigInteger('kembalian')
                ->default(0);

            $table->string('status', 20)
                ->default('selesai');

            $table->timestamps();

            $table->index([
                'tanggal_transaksi',
                'status'
            ]);

            $table->index('metode_pembayaran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
