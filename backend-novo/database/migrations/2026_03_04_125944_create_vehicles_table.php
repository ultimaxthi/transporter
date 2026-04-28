<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate', 10)->unique();
            $table->string('brand');
            $table->string('model');
            $table->year('year');
            $table->string('type', 50)->nullable();
            $table->string('patrimony_number')->nullable()->unique();
            $table->integer('current_odometer')->default(0);
            $table->enum('status', [
                'available',
                'in_trip',
                'in_maintenance',
                'inactive'
            ])->default('available')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['brand', 'model']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};