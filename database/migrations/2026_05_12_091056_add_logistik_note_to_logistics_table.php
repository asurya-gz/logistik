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
            $table->text('logistik_note')->nullable()->after('office_note');
            $table->foreignId('logistik_noted_by')->nullable()->constrained('users')->nullOnDelete()->after('logistik_note');
            $table->timestamp('logistik_noted_at')->nullable()->after('logistik_noted_by');
        });
    }

    public function down(): void
    {
        Schema::table('logistics', function (Blueprint $table) {
            $table->dropConstrainedForeignId('logistik_noted_by');
            $table->dropColumn(['logistik_note', 'logistik_noted_at']);
        });
    }
};
