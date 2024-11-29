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
        Schema::create('soal_tryouts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tryout_id')->constrained('tryouts')->onDelete('cascade');
            $table->foreignId('bidang_id')->constrained('bidang_tryouts')->onDelete('cascade');
            $table->foreignId('kompetensi_id')->constrained('kompetensi_tryouts')->onDelete('cascade');
            $table->integer('nomor');
            $table->text('soal');
            $table->text('pilihan_a');
            $table->text('pilihan_b');
            $table->text('pilihan_c');
            $table->text('pilihan_d');
            $table->text('pilihan_e');
            $table->text('jawaban');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soal_tryouts');
    }
};
