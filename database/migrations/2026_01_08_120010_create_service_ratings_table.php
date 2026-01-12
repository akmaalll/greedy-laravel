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
        Schema::create('tbl_rating_layanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('tbl_pesanan')->onDelete('cascade');
            $table->foreignId('klien_id')->constrained('users')->onDelete('cascade');
            $table->integer('rating')->unsigned()->comment('Rating 1-5');
            $table->text('ulasan')->nullable();
            $table->timestamps();

            $table->index('rating');
            $table->unique('pesanan_id'); // Satu pesanan hanya bisa rating layanan sekali
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_rating_layanan');
    }
};
