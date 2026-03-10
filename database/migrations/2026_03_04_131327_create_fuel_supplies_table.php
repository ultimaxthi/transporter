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
        Schema::create('fuel_supplies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();

            $table->foreignId('driver_id')->constrained('users')->cascadeOnDelete();

            $table->string('fuel_station')->nullable();
            $table->string('fuel_type')->nullable();
            $table->decimal('liters', 8, 2);
            $table->decimal('price_per_liter', 8, 2);
            $table->decimal('total_cost', 10, 2);

            $table->integer('odometer');

            $table->timestamp('supplied_at')->useCurrent();

            $table->timestamps();

            $table->index(['vehicle_id', 'supplied_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_supplies');
    }
};
