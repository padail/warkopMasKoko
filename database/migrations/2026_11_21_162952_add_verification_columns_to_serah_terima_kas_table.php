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
        Schema::table('serah_terima_kas', function (Blueprint $table) {
            $table->foreignId('diterima_oleh')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->text('catatan')->nullable()->after('status');
            $table->timestamp('diserahkan_pada')->nullable()->after('catatan');
            $table->timestamp('diverifikasi_pada')->nullable()->after('diserahkan_pada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('serah_terima_kas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('diterima_oleh');
            $table->dropColumn(['catatan', 'diserahkan_pada', 'diverifikasi_pada']);
        });
    }
};
