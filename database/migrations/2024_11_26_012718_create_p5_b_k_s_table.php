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
        Schema::create('p5_b_k_s', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->default(0); // Set the default value for status
            $table->enum('dimensi', ['iman', 'kebhinekaan', 'mandiri', 'gotong-royong', 'kritis-kreatif'])->nullable();
            $table->enum('capaian', ['MB', 'SB', 'BSH', 'SAB'])->nullable();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('semester_id')->nullable()->constrained('semesters')->onDelete('set null'); // Make semester_id nullable
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p5_b_k_s');
    }
};
