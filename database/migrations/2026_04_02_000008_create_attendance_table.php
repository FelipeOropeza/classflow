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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments')->cascadeOnDelete();
            $table->foreignId('class_diary_id')->constrained('class_diaries')->cascadeOnDelete();
            $table->enum('status', ['present', 'absent', 'late'])->default('present');
            $table->text('observation')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->timestamps();

            // Constraint de unicidade: Aluno + Diário (Chamada única por aula)
            $table->unique(['enrollment_id', 'class_diary_id'], 'unique_attendance_per_diary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
