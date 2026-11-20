<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('serah_terima_kas', function (Blueprint $table) {
            $table->unsignedBigInteger('tunai_harus_disetor')->default(0);
            $table->unsignedBigInteger('tunai_aktual')->default(0);
            $table->bigInteger('selisih_tunai')->default(0);
            $table->unsignedBigInteger('non_tunai_harus_disetor')->default(0);
            $table->unsignedBigInteger('non_tunai_aktual')->default(0);
            $table->bigInteger('selisih_non_tunai')->default(0);
        });

        DB::table('serah_terima_kas')->update([
            'tunai_harus_disetor' => DB::raw('uang_harus_disetor'),
            'tunai_aktual' => DB::raw('uang_aktual'),
            'selisih_tunai' => DB::raw('selisih'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('serah_terima_kas', function (Blueprint $table) {
            $table->dropColumn([
                'tunai_harus_disetor',
                'tunai_aktual',
                'selisih_tunai',
                'non_tunai_harus_disetor',
                'non_tunai_aktual',
                'selisih_non_tunai',
            ]);
        });
    }
};
