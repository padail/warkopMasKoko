
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_transaksi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaksi_id')
                ->constrained('transaksi')
                ->cascadeOnDelete();

            $table->foreignId('menu_barang_id')
                ->constrained('menu_barang')
                ->restrictOnDelete();

            $table->string('nama_item_snapshot', 120);

            $table->unsignedBigInteger('harga_satuan');

            $table->decimal('jumlah', 12, 3);

            $table->unsignedBigInteger('subtotal');

            $table->timestamps();

            $table->index('menu_barang_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi');
    }
};
