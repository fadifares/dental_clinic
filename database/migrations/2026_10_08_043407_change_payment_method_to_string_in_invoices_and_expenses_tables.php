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
        if (Schema::hasTable('invoices') && Schema::hasColumn('invoices', 'payment_method')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('payment_method', 50)->default('card')->change();
            });
        }

        if (Schema::hasTable('expenses') && Schema::hasColumn('expenses', 'payment_method')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->string('payment_method', 50)->default('cash')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('invoices') && Schema::hasColumn('invoices', 'payment_method')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'installments'])->default('card')->change();
            });
        }

        if (Schema::hasTable('expenses') && Schema::hasColumn('expenses', 'payment_method')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->enum('payment_method', ['cash', 'card', 'bank_transfer'])->default('cash')->change();
            });
        }
    }
};
