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
        Schema::table('logistics_photos', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('logistics_photos', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
