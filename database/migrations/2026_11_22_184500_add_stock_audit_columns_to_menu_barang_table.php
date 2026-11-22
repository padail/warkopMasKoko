<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_barang', function (Blueprint $table): void {
            $table->foreignId('stok_diperbarui_oleh')
                ->nullable()
                ->after('is_active')
                ->constrained('users')
                ->nullOnDelete();
            $table->string('alasan_perubahan_stok', 120)
                ->nullable()
                ->after('stok_diperbarui_oleh');
            $table->timestamp('stok_diperbarui_pada')
                ->nullable()
                ->after('alasan_perubahan_stok');
        });
    }

    public function down(): void
    {
        Schema::table('menu_barang', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('stok_diperbarui_oleh');
            $table->dropColumn(['alasan_perubahan_stok', 'stok_diperbarui_pada']);
        });
    }
};
