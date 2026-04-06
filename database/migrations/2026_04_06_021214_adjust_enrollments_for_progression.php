<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreignId('class_id')->nullable()->change();
            $table->string('status')->default('active')->after('academic_year_id'); // active, pending, approved, retained, completed
            $table->decimal('final_score', 5, 2)->nullable()->after('status');
            $table->decimal('attendance_percentage', 5, 2)->nullable()->after('final_score');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreignId('class_id')->nullable(false)->change();
            $table->dropColumn(['status', 'final_score', 'attendance_percentage']);
        });
    }
};
