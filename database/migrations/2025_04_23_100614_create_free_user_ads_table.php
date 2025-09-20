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
        Schema::create('free_user_ads', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->default(0);
            $table->bigInteger('user_id')->nullable();
            $table->integer('total_ads')->default(0);
            $table->integer('used_ads')->default(0);
            $table->integer('remaining_ads')->virtualAs('total_ads - used_ads');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('free_user_ads');
    }
};
