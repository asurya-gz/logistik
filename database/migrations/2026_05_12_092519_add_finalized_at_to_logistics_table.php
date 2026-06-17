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
        Schema::table('logistics', function (Blueprint $table) {
            $table->timestamp('finalized_at')->nullable()->after('logistik_noted_at');
            $table->foreignId('finalized_by')->nullable()->constrained('users')->nullOnDelete()->after('finalized_at');
        });
    }

    public function down(): void
    {
        Schema::table('logistics', function (Blueprint $table) {
            $table->dropConstrainedForeignId('finalized_by');
            $table->dropColumn('finalized_at');
        });
    }
};
