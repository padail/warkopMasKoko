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
        Schema::create('serah_terima_kas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal_serah');
            $table->unsignedBigInteger('uang_harus_disetor');
            $table->unsignedBigInteger('uang_aktual');
            $table->bigInteger('selisih');
            $table->string('status', 20)->default('menunggu');
            $table->timestamps();

            $table->unique(['user_id', 'tanggal_serah']);
            $table->index(['tanggal_serah', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serah_terima_kas');
    }
};
