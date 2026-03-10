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
        Schema::create('maintenaces', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();

            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();

            $table->string('description')->nullable();

            $table->timestamps();

            $table->index(['vehicle_id', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenaces');
    }
};
