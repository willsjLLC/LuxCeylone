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
        Schema::create('user_rank_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('current_rank_id')->nullable();
            $table->integer('level_one_user_count')->default(0);
            $table->integer('level_two_user_count')->default(0);
            $table->integer('level_three_user_count')->default(0);
            $table->integer('level_four_user_count')->default(0);
            $table->boolean('required_at_least_one_product_purchase')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_rank_details');
    }
};
