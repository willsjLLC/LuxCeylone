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
        Schema::create('advertisement_boosted_histories', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('advertisement_id')->nullable();
            $table->bigInteger('user_package_id')->nullable();
            $table->tinyInteger('is_package_boost')->nullable();
            $table->bigInteger('boost_package_id')->nullable();
            $table->bigInteger('payment_option_id')->nullable();
            $table->tinyInteger('is_free_advertisement')->nullable();
            $table->decimal('price')->nullable();
            $table->dateTime('boosted_date')->nullable();
            $table->dateTime('expiry_date')->nullable();
            $table->dateTime('remaining')->virtualAs('boosted_date - expiry_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisement_boosted_histories');
    }
};
