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
        Schema::create('dental_charts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('tooth_number'); // FDI format 11..48
            $table->string('condition')->default('healthy'); // healthy, caries, filled, crown, missing, implant, rct
            $table->string('surfaces')->nullable(); // M, D, O, B, L
            $table->string('procedure_done')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['patient_id', 'tooth_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dental_charts');
    }
};
