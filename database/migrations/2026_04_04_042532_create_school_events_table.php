<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_events', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $blueprint->string('title');
            $blueprint->text('description')->nullable();
            $blueprint->date('event_date');
            $blueprint->string('type')->default('event'); // event, holiday, meeting, exam_week
            $blueprint->string('color_hex')->default('#4F46E5');
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_events');
    }
};
