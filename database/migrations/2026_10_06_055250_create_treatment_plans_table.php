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
        Schema::create('treatment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->decimal('total_cost', 10, 2)->default(0.00);
            $table->unsignedTinyInteger('stages_count')->default(1);
            $table->unsignedTinyInteger('completed_stages')->default(0);
            $table->enum('status', ['proposed', 'approved', 'in_progress', 'completed', 'cancelled'])->default('in_progress');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_plans');
    }
};
