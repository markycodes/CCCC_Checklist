<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('student_id', 50);
            $table->string('name');
            $table->unsignedSmallInteger('entry_year');
            $table->unsignedSmallInteger('expected_graduation_year')->nullable();
            $table->string('previous_school')->nullable();
            $table->string('last_year_attended', 50)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'student_id']);
        });

        Schema::create('student_course_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('course_code', 50);
            $table->timestamps();

            $table->unique(['student_id', 'course_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_course_completions');
        Schema::dropIfExists('students');
    }
};
