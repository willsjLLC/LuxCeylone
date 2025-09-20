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
        Schema::create('advertisement_boosted_locations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('advertisement_boosted_history_id');
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('boost_package_id')->nullable();
            $table->bigInteger('advertisement_id')->nullable();
            $table->bigInteger('district_id');
            $table->bigInteger('city_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisement_boosted_locations');
    }
};
