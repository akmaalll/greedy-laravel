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
        Schema::create('tbl_jadwal_pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('tbl_pesanan')->onDelete('cascade');
            $table->dateTime('jadwal_mulai');
            $table->dateTime('jadwal_selesai');
            $table->dateTime('aktual_mulai')->nullable();
            $table->dateTime('aktual_selesai')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('jadwal_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_jadwal_pesanan');
    }
};
