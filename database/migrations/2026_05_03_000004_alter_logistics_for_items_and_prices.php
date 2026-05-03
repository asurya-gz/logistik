<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logistics', function (Blueprint $table) {
            $table->foreignId('item_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->decimal('unit_price_snapshot', 15, 2)->nullable()->after('jumlah');
            $table->decimal('total_price', 15, 2)->nullable()->after('unit_price_snapshot');
        });
    }

    public function down(): void
    {
        Schema::table('logistics', function (Blueprint $table) {
            $table->dropConstrainedForeignId('item_id');
            $table->dropColumn(['unit_price_snapshot', 'total_price']);
        });
    }
};
