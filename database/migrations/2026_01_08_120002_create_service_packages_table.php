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
        Schema::create('tbl_paket_layanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layanan_id')->constrained('tbl_layanan')->onDelete('cascade');
            $table->string('nama', 100);
            $table->text('deskripsi')->nullable();
            $table->enum('tipe_durasi', ['jam', 'foto', 'menit']);
            $table->integer('nilai_durasi');
            $table->decimal('harga_dasar', 15, 2);
            $table->decimal('harga_overtime', 15, 2)->nullable();
            $table->enum('satuan_overtime', ['jam', 'foto', 'menit'])->nullable();
            $table->json('fitur')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            $table->index('tipe_durasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_paket_layanan');
    }
};
