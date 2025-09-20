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
        Schema::table('advertisement_packages', function (Blueprint $table) {
            $table->bigInteger('boost_package_id')->nullable()->after('includes_boost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advertisement_packages', function (Blueprint $table) {
            //
        });
    }
};
