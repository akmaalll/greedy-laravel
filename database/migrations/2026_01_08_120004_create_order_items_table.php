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
        Schema::create('tbl_item_pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('tbl_pesanan')->onDelete('cascade');
            $table->foreignId('paket_layanan_id')->constrained('tbl_paket_layanan')->onDelete('cascade');
            $table->integer('jumlah')->default(1);
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('jam_overtime', 5, 2)->nullable()->default(0);
            $table->decimal('biaya_overtime', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_item_pesanan');
    }
};
