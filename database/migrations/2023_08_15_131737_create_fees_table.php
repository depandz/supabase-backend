<?php

use App\Models\Province;
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

        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Province::class);
            $table->float('heavy')->default('0');
            $table->float('light')->default('0');
            $table->float('truck')->default('0');
            $table->unsignedInteger('full_percentage');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
