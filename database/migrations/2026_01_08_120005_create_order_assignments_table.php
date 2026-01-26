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
        Schema::create('tbl_penugasan_fotografer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('tbl_pesanan')->onDelete('cascade');
            $table->foreignId('fotografer_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['ditugaskan', 'diterima', 'ditolak', 'selesai'])->default('ditugaskan');
            $table->text('catatan')->nullable();
            $table->timestamp('waktu_ditugaskan')->nullable();
            $table->timestamp('waktu_diterima')->nullable();
            $table->timestamp('waktu_selesai')->nullable();
            $table->timestamps();

            $table->unique(['pesanan_id', 'fotografer_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_penugasan_fotografer');
    }
};
