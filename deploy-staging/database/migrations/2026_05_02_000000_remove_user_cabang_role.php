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
            ->where('role', 'user_cabang')
            ->update(['role' => 'lapangan']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
