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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_subject_id')->constrained('class_subject')->cascadeOnDelete();
            $table->tinyInteger('day_of_week')->comment('1=Monday, 5=Friday');
            $table->tinyInteger('period')->comment('1=1st Class, 2=2nd Class, etc.');
            $table->timestamps();

            // A class_subject shouldn't be duplicated exactly on the same day and period.
            $table->unique(['class_subject_id', 'day_of_week', 'period'], 'schedule_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
