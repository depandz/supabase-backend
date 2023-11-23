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
        Schema::table('pages', function (Blueprint $table) {
            $table->string('title_fr');
            $table->string('title_ar');
            $table->mediumText('content_fr');
            $table->mediumText('content_ar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('title_fr');
            $table->string('sub_title_fr')->nullable();
            $table->dropColumn('title_ar');
            $table->string('sub_title_ar')->nullable();
            $table->dropColumn('content_fr');
            $table->dropColumn('content_ar');
        });
    }
};
