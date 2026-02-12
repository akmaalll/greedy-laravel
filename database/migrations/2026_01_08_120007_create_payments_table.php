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
        Schema::create('tbl_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('tbl_pesanan')->onDelete('cascade');
            $table->enum('metode_pembayaran', ['transfer', 'tunai', 'ewallet']);
            $table->decimal('jumlah', 15, 2);
            $table->enum('status', ['menunggu', 'lunas', 'gagal', 'dikembalikan'])->default('menunggu');
            $table->timestamp('waktu_bayar')->nullable();
            $table->string('bukti_gambar', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_pembayaran');
    }
};
