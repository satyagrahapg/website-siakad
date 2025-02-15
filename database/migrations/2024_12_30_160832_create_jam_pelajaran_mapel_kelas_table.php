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
        Schema::create('jam_pelajaran_mapel_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jampel_id')->constrained('jam_pelajaran')->onDelete('cascade');
            $table->foreignId('mapel_kelas_id')->constrained('mapel_kelas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jam_pelajaran_mapel_kelas');
    }
};
