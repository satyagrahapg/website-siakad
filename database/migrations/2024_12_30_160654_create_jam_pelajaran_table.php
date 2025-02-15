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
        Schema::create('jam_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->enum('hari', ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"]);
            $table->integer('nomor')->nullable();
            $table->string('event')->nullable();
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->time('jam_mulai_calendar')->nullable();
            $table->time('jam_selesai_calendar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jam_pelajaran');
    }
};
