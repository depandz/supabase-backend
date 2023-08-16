<?php

use App\Models\Company;
use App\Models\Province;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('s_id')->unique();
            $table->string('full_name');
            $table->string('phone_number');
            $table->enum('gender',['male','female'])->default('male')->comment('male or female');
            $table->integer('identity_card_number')->unique();
            $table->mediumInteger('licence_plate')->nullable();
            $table->string('photo')->nullable();
             $table->foreignIdFor(Province::class)->nullable();
            $table->json('location')->nullable();
            $table->string('email')->unique()->nullable();
            $table->boolean('is_online')->comment('0  is offline , 1 is online');
            $table->unsignedInteger('reported_count');
            $table->string('messaging_token')->nullable();
            $table->enum('account_status', ["pending","active","suspended"])->default('pending')->comment('pending / suspended / active');
            $table->enum('vehicle_type', ["light","heavy","truck"])->default('light')->comment('light / heavy / truck');
            $table->mediumInteger('commercial_register_number');
            $table->unsignedInteger('capacity');
            $table->foreignIdFor(Company::class)->nullable();
            $table->boolean('is_default_for_company')->default(false);
            $table->boolean('can_transport_goods')->default(false);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
