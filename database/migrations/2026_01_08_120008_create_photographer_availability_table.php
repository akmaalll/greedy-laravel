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
        Schema::create('tbl_ketersediaan_fotografer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fotografer_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->enum('status', ['tersedia', 'tidak_tersedia', 'dipesan'])->default('tersedia');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['fotografer_id', 'tanggal']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_ketersediaan_fotografer');
    }
};
