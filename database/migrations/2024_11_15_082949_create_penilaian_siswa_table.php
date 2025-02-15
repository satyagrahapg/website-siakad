<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('penilaian_siswa', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status');
            $table->float('nilai')->nullable();
            $table->float('remedial')->nullable();
            $table->float('nilai_akhir')->nullable();
            $table->foreignId('penilaian_id')->constrained('penilaians')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('penilaian_siswa');
    }
};
