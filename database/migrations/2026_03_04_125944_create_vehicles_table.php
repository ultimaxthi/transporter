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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            $table->string('plate',10)->unique();
            $table->string('brand');
            $table->string('model');
            $table->year('year');
            $table->string('type',50);
            // tipo: senda, van, micro-ônibus, ônibus

            $table->string('patrimony_number')->unique();

            $table->integer('current_odometer')->default(0);

            $table->timestamps();

            $table->index(['brand', 'model']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
