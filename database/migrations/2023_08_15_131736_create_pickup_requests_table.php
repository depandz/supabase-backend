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
        Schema::disableForeignKeyConstraints();

        Schema::create('pickup_requests', function (Blueprint $table) {
            $table->id();
            $table->string('sid')->unique()->nullable();
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->foreign('driver_id')->references('id')->on('drivers');
            $table->json('destination')->nullable();
            $table->json('location')->nullable();
            $table->dateTime('date_requested');
            $table->double('estimated_price');
            $table->float('estimated_distance');
            $table->integer('estimated_time');
            $table->enum('status', ["0","1","2","3","4"])->comment('\\\"{\\n    initialized = 0,\\n    pending = 1,\\n    approved = 2,\\n    cancelled = 3,\\n    validated = 4,\\n}\\\"');
            $table->double('total');
            $table->enum('vehicle_type', ["light","heavy","truck"])->default('light')->comment('light / heavy / truck');
            $table->boolean('is_vehicle_empty')->default(true);
            $table->mediumInteger('vehicle_licence_plate');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickup_requests');
    }
};
