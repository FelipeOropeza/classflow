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
        // 1. Tornar data obrigatória nas avaliações
        Schema::table('assessments', function (Blueprint $table) {
            $table->date('date')->change(); // Já era nullable, agora será not null se eu não colocar nullable()
        });

        // 2. Trava de chamadas (Attendance)
        Schema::table('attendance', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('status');
            // Garantir que um aluno não tenha duas chamadas para a mesma matéria no mesmo dia
            $table->unique(['enrollment_id', 'subject_id', 'date'], 'unique_attendance_per_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->date('date')->nullable()->change();
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->dropUnique('unique_attendance_per_day');
            $table->dropColumn('is_locked');
        });
    }
};
