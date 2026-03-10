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
        Schema::create('trips', function (Blueprint $table) {

            $table->id();

            $table->string('patient_name');

            $table->string('priority',20)
                ->default('normal')
                ->index();
            // normal, high, emergency

            $table->string('origin_street');
            $table->string('origin_neighborhood');

            $table->string('destination_street');
            $table->string('destination_neighborhood');
            $table->string('destination_city')->nullable();

            $table->text('observations')->nullable();

            $table->string('status',30)->index();
            // pending, assigned, in_progress, completed, cancelled

            $table->integer('queue_position')->nullable();

            $table->foreignId('operator_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('driver_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('vehicle_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->integer('initial_odometer')->nullable();
            $table->integer('final_odometer')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index(['driver_id', 'status']);
            $table->index(['status','created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
