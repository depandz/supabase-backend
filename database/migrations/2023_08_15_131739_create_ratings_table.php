<?php

use App\Models\Client;
use App\Models\Driver;
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

        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Client::class);
            $table->foreignIdFor(Driver::class);
            $table->enum('value',[1,2,3,4,5])->comment('rating from 1 to 5');
            $table->string('comment')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
