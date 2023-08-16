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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('s_id')->nullable()->change();
            $table->string('full_name')->nullable()->change();
            $table->unsignedInteger('reported_count')->nullable()->change();
        });
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('s_id')->nullable()->change();
            $table->string('full_name')->nullable()->change();
            $table->unsignedInteger('reported_count')->nullable()->change();
            $table->integer('identity_card_number')->nullable()->change();
            $table->boolean('is_online')->nullable()->change();
            $table->integer('commercial_register_number')->nullable()->change();
            $table->unsignedInteger('capacity')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
