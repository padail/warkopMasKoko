
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_barang', function (Blueprint $table) {
            $table->id();

            $table->string('nama', 100);
            $table->string('kategori', 50);

            $table->unsignedBigInteger('harga_jual')
                ->default(0);

            $table->decimal('stok', 12, 3)
                ->default(0);

            $table->string('satuan', 30)
                ->default('pcs');

            $table->decimal('stok_minimum', 12, 3)
                ->default(0);

            $table->boolean('is_dijual')
                ->default(true);

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index('nama');
            $table->index(['kategori', 'is_active']);
            $table->index(['is_dijual', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_barang');
    }
};
