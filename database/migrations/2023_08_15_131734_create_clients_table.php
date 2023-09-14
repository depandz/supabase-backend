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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('s_id')->unique();
            $table->string('full_name');
            $table->string('phone_number');
            $table->enum('gender',['male','female'])->default('male')->comment('male or female');
            $table->json('location')->nullable();
            $table->string('email')->nullable();
            $table->bigInteger('photo')->nullable();
            $table->string('messaging_token')->nullable();
            $table->unsignedInteger('reported_count');
            $table->enum('account_status', ["pending","active","suspended"])->default('pending')->comment('pending / active / suspended');
            $table->dateTime('registered_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
