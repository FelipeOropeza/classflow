<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropUnique('unique_attendance_per_day');
            $table->dropForeign(['subject_id']);
            $table->dropColumn(['subject_id', 'date']);
            $table->foreignId('class_diary_id')->after('enrollment_id')->constrained('class_diaries')->cascadeOnDelete();
            $table->unique(['enrollment_id', 'class_diary_id'], 'unique_attendance_per_diary');
        });
    }

    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropUnique('unique_attendance_per_diary');
            $table->dropForeign(['class_diary_id']);
            $table->dropColumn('class_diary_id');
            $table->foreignId('subject_id')->after('enrollment_id')->constrained()->cascadeOnDelete();
            $table->date('date')->after('subject_id');
            $table->unique(['enrollment_id', 'subject_id', 'date'], 'unique_attendance_per_day');
        });
    }
};
