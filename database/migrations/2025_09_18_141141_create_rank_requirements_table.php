<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rank_requirements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('rank_id')->nullable();
            $table->bigInteger('min_rank_id')->nullable();
            $table->integer('level_one_user_count')->nullable();
            $table->integer('level_two_user_count')->nullable();
            $table->integer('level_three_user_count')->nullable();
            $table->integer('level_four_user_count')->nullable();
            $table->boolean('required_at_least_one_product_purchase')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rank_requirements');
    }
};
