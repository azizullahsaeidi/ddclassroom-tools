<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('skipped_grade_subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('sub_grade_id');
            $table->unsignedSmallInteger('year')->index();
            $table->unsignedSmallInteger('subject_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('note')->nullable();

            // Foreign Keys
            $table->foreign('sub_grade_id')->references('id')->on('sub_grades');

            $table->foreign('subject_id')->references('id')->on('subjects');

            $table->foreign('user_id')->references('id')->on('users');

            $table->unique(['sub_grade_id', 'subject_id', 'year']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skipped_grade_subjects');
    }
};
