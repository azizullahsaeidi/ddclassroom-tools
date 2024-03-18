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
        Schema::create('grade_subjects', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('subject_id');
            $table->unsignedTinyInteger('grade_id');

            // Foreign Keys

            $table->foreign('grade_id')
                ->references('id')
                ->on('grades');

            $table->foreign('subject_id')
                ->references('id')
                ->on('subjects');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_subjects');
    }
};
