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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments')->cascadeOnDelete();
            $table->foreignId('assessment_id')->nullable()->constrained('assessments')->cascadeOnDelete();
            $table->decimal('score', 5, 2)->nullable();
            $table->boolean('is_locked')->default(false);
            $table->timestamps();

            // Garantimos que cada aluno tenha apenas uma nota POR AVALIAÇÃO
            $table->unique(['enrollment_id', 'assessment_id'], 'unique_grade_per_student_assessment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
