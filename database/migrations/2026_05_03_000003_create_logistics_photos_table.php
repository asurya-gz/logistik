<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('logistics_id')->constrained()->cascadeOnDelete();
            $table->string('photo_path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_photos');
    }
};
