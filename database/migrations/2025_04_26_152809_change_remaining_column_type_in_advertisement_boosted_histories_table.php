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
        Schema::table('advertisement_boosted_histories', function (Blueprint $table) {
            $table->dropColumn('remaining');
        });

        Schema::table('advertisement_boosted_histories', function (Blueprint $table) {
            // Add new integer column
            $table->integer('remaining')->after('expiry_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advertisement_boosted_histories', function (Blueprint $table) {
            // 
        });
    }
};
