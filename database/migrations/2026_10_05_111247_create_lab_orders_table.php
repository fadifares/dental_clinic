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
        Schema::create('lab_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->string('lab_name'); // e.g. Elite Dental Lab
            $table->string('item_type'); // e.g. Zirconia Crown, E-max Bridge, Night Guard
            $table->string('tooth_numbers')->nullable(); // e.g. 16, 17
            $table->string('shade')->nullable(); // e.g. A1, A2, BL2
            $table->decimal('cost', 10, 2)->default(0.00);
            $table->date('order_date');
            $table->date('expected_delivery_date')->nullable();
            $table->enum('status', ['sent', 'in_progress', 'ready', 'delivered', 'cancelled'])->default('sent');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_orders');
    }
};
