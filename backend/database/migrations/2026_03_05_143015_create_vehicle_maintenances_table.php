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
    Schema::create('vehicle_maintenances', function (Blueprint $table) {
        $table->id();

        $table->foreignId('vehicle_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->string('type'); 
        // preventiva, corretiva, revisão etc

        $table->text('description')->nullable();

        $table->integer('odometer')->nullable();

        $table->decimal('cost',10,2)->nullable();

        $table->date('start_date');
        $table->date('end_date')->nullable();

        $table->boolean('active')->default(true);

        $table->foreignId('created_by')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();

        $table->timestamps();

        $table->index(['vehicle_id','active']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_maintenances');
    }
};
