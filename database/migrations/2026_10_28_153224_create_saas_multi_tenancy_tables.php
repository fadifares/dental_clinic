<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create clinics table if not exists
        if (! Schema::hasTable('clinics')) {
            Schema::create('clinics', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->text('address')->nullable();
                $table->string('status')->default('active'); // active, suspended
                $table->unsignedInteger('max_users')->default(10);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 2. Add SaaS columns to users table
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'clinic_id')) {
                $table->foreignId('clinic_id')->nullable()->after('id')->constrained('clinics')->nullOnDelete();
            }
            if (! Schema::hasColumn('users', 'is_super_admin')) {
                $table->boolean('is_super_admin')->default(false)->after('role');
            }
            if (! Schema::hasColumn('users', 'is_primary_owner')) {
                $table->boolean('is_primary_owner')->default(false)->after('is_super_admin');
            }
            if (! Schema::hasColumn('users', 'last_login_at')) {
                $table->dateTime('last_login_at')->nullable()->after('is_active');
            }
            if (! Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip')->nullable()->after('last_login_at');
            }
        });

        // 3. Add clinic_id to operational tables
        $tables = [
            'patients',
            'doctors',
            'appointments',
            'dental_charts',
            'lab_orders',
            'invoices',
            'treatment_plans',
            'expenses',
            'settings',
        ];

        foreach ($tables as $tbl) {
            if (Schema::hasTable($tbl) && ! Schema::hasColumn($tbl, 'clinic_id')) {
                Schema::table($tbl, function (Blueprint $table) {
                    $table->foreignId('clinic_id')->nullable()->constrained('clinics')->cascadeOnDelete();
                });
            }
        }

        // 4. Create clinic_page_visits table for traffic and top visited pages
        if (! Schema::hasTable('clinic_page_visits')) {
            Schema::create('clinic_page_visits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('route_name')->nullable();
                $table->string('url_path');
                $table->string('page_title')->nullable();
                $table->string('method', 10)->default('GET');
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index(['clinic_id', 'created_at']);
                $table->index(['clinic_id', 'url_path']);
            });
        }

        // 5. Create clinic_user_activities table for user actions and logs
        if (! Schema::hasTable('clinic_user_activities')) {
            Schema::create('clinic_user_activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action_type', 50); // login, logout, create, update, delete, view
                $table->text('description');
                $table->json('details')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index(['clinic_id', 'created_at']);
                $table->index(['clinic_id', 'user_id']);
                $table->index(['clinic_id', 'action_type']);
            });
        }

        // 6. Migrate existing data to Clinic #1
        $existingClinic = DB::table('clinics')->first();
        if (! $existingClinic) {
            $clinicId = DB::table('clinics')->insertGetId([
                'name' => 'العيادة الأولى',
                'slug' => 'clinic-1',
                'phone' => '+201553408200',
                'email' => 'info@dentalclinic.com',
                'address' => 'الفرع الرئيسي',
                'status' => 'active',
                'max_users' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $clinicId = $existingClinic->id;
        }

        // Assign all existing records to Clinic #1
        foreach ($tables as $tbl) {
            if (Schema::hasTable($tbl)) {
                DB::table($tbl)->whereNull('clinic_id')->update(['clinic_id' => $clinicId]);
            }
        }

        // Assign users to Clinic #1
        DB::table('users')->whereNull('clinic_id')->update(['clinic_id' => $clinicId]);

        // Mark the first admin as Primary Owner and Super Admin
        $firstAdmin = DB::table('users')
            ->where(function ($query) {
                $query->where('role', 'admin')
                    ->orWhere('roles', 'like', '%admin%');
            })
            ->orderBy('id', 'asc')
            ->first();

        if ($firstAdmin) {
            DB::table('users')->where('id', $firstAdmin->id)->update([
                'is_primary_owner' => true,
                'is_super_admin' => true,
            ]);
        } else {
            $firstUser = DB::table('users')->orderBy('id', 'asc')->first();
            if ($firstUser) {
                DB::table('users')->where('id', $firstUser->id)->update([
                    'is_primary_owner' => true,
                    'is_super_admin' => true,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_user_activities');
        Schema::dropIfExists('clinic_page_visits');

        $tables = [
            'patients',
            'doctors',
            'appointments',
            'dental_charts',
            'lab_orders',
            'invoices',
            'treatment_plans',
            'expenses',
            'settings',
        ];

        foreach ($tables as $tbl) {
            if (Schema::hasTable($tbl) && Schema::hasColumn($tbl, 'clinic_id')) {
                Schema::table($tbl, function (Blueprint $table) {
                    $table->dropForeign(['clinic_id']);
                    $table->dropColumn('clinic_id');
                });
            }
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'clinic_id')) {
                    $table->dropForeign(['clinic_id']);
                    $table->dropColumn('clinic_id');
                }
                if (Schema::hasColumn('users', 'is_super_admin')) {
                    $table->dropColumn('is_super_admin');
                }
                if (Schema::hasColumn('users', 'is_primary_owner')) {
                    $table->dropColumn('is_primary_owner');
                }
                if (Schema::hasColumn('users', 'last_login_at')) {
                    $table->dropColumn('last_login_at');
                }
                if (Schema::hasColumn('users', 'last_login_ip')) {
                    $table->dropColumn('last_login_ip');
                }
            });
        }

        Schema::dropIfExists('clinics');
    }
};
