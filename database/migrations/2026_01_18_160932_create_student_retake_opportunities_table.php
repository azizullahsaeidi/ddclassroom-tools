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
        Schema::create('student_retake_opportunities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedSmallInteger('sub_grade_id');
            $table->unsignedSmallInteger('year_id');
            $table->unsignedSmallInteger('subject_id');
            $table->unsignedDecimal('score')->nullable();
            $table->unsignedDecimal('second_chance_score')->nullable();
            $table->unsignedDecimal('third_chance_score')->nullable();
            $table->date('second_chance_date')->nullable();
            $table->date('third_chance_date')->nullable();
            $table->boolean('is_passed')->default(false)->index();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('student_id')
                ->references('id')
                ->on('students');

            $table->foreign('sub_grade_id')
                ->references('id')
                ->on('sub_grades');

            $table->foreign('year_id')
                ->references('id')
                ->on('years');

            $table->foreign('subject_id')
                ->references('id')
                ->on('subjects');

            // Unique constraint
            $table->unique(['student_id', 'sub_grade_id', 'year_id', 'subject_id'], 'student_retake_opportunity_unique_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_retake_opportunities');
    }
};
