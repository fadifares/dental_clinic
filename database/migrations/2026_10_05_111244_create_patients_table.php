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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('file_number')->unique(); // e.g. PT-1001
            $table->string('name');
            $table->string('phone');
            $table->string('national_id')->nullable();
            $table->enum('gender', ['male', 'female'])->default('male');
            $table->date('date_of_birth')->nullable();
            $table->text('medical_history')->nullable();
            $table->text('allergies')->nullable(); // e.g. Penicillin allergy
            $table->text('chronic_diseases')->nullable(); // e.g. Diabetes, Hypertension
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
