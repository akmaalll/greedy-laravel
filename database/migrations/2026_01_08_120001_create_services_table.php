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
        Schema::create('tbl_layanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('tbl_kategori')->onDelete('cascade');
            $table->string('nama', 100);
            $table->enum('tipe', ['fotografi', 'videografi']);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            $table->index('tipe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_layanan');
    }
};
