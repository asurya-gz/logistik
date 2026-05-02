<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')
            ->where('role', 'super_admin')
            ->update(['role' => 'kantor']);

        DB::table('users')
            ->where('role', 'admin_cabang')
            ->update(['role' => 'logistik']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')
            ->where('role', 'kantor')
            ->update(['role' => 'super_admin']);

        DB::table('users')
            ->where('role', 'logistik')
            ->update(['role' => 'admin_cabang']);
    }
};
