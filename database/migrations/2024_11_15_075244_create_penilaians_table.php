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
        Schema::create('penilaians', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe', ['Tugas', 'UH', 'STS', 'SAS']);
            $table->string('judul');
            $table->integer('kktp');
            $table->date('tanggal');
            $table->string('keterangan')->nullable();
            $table->foreignId('mapel_kelas_id')->constrained('mapel_kelas')->onDelete('cascade');
            $table->timestamps();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaians');
    }
};
