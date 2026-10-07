<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('user_type', ['doctor', 'administrative'])->default('administrative')->after('role');
            $table->string('speciality')->nullable()->after('user_type');
            $table->string('phone')->nullable()->after('email');
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
        });

        // Set existing doctor users
        DB::table('users')->where('role', 'doctor')->orWhere('id', 1)->update([
            'user_type' => 'doctor',
        ]);

        DB::table('users')->where('id', 1)->update([
            'speciality' => 'استشاري جراحة وزراعة الأسنان',
            'phone' => '0500000001',
        ]);

        DB::table('users')->where('id', 2)->update([
            'speciality' => 'أخصائية تجميل وابتسامة هوليود',
            'phone' => '0500000002',
        ]);

        // Link Doctor 1 and 2
        DB::table('doctors')->where('id', 1)->update(['user_id' => 1]);
        DB::table('doctors')->where('id', 2)->update(['user_id' => 2]);

        // Create Users for Doctor 3 & 4 if they don't have user accounts yet
        $doc3 = DB::table('doctors')->where('id', 3)->first();
        if ($doc3 && ! DB::table('users')->where('email', $doc3->email)->exists()) {
            $doc3UserId = DB::table('users')->insertGetId([
                'name' => $doc3->name,
                'email' => $doc3->email ?? 'dr.khalid@dentalcare.com',
                'password' => Hash::make('Password123#'),
                'role' => 'doctor',
                'roles' => json_encode(['doctor']),
                'user_type' => 'doctor',
                'speciality' => $doc3->speciality,
                'phone' => $doc3->phone ?? '0500000003',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('doctors')->where('id', 3)->update(['user_id' => $doc3UserId]);
        }

        $doc4 = DB::table('doctors')->where('id', 4)->first();
        if ($doc4 && ! DB::table('users')->where('email', $doc4->email)->exists()) {
            $doc4UserId = DB::table('users')->insertGetId([
                'name' => $doc4->name,
                'email' => $doc4->email ?? 'dr.reem@dentalcare.com',
                'password' => Hash::make('Password123#'),
                'role' => 'doctor',
                'roles' => json_encode(['doctor']),
                'user_type' => 'doctor',
                'speciality' => $doc4->speciality,
                'phone' => $doc4->phone ?? '0500000004',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('doctors')->where('id', 4)->update(['user_id' => $doc4UserId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['user_type', 'speciality', 'phone']);
        });
    }
};
