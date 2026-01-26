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
        Schema::create('tbl_pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pesanan', 50)->unique();
            $table->foreignId('klien_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal_acara');
            $table->time('waktu_mulai_acara');
            $table->time('waktu_selesai_acara')->nullable();
            $table->string('lokasi_acara', 255);
            $table->text('deskripsi_acara')->nullable();
            $table->enum('status', ['menunggu', 'dikonfirmasi', 'berlangsung', 'selesai', 'dibatalkan'])->default('menunggu');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('biaya_overtime', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('tanggal_acara');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_pesanan');
    }
};
