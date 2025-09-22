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
        Schema::create('claimed_rank_rewards', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('current_rank_id')->nullable();
            $table->tinyInteger('rank_one_status')->comment('0 => pending, 1 => achieved')->default(0);
            $table->tinyInteger('rank_one_claimed_status')->comment('0 => rank not satisfied, 1 => pending (after satisfied rank), 2 => processing,  3 => complete, 4 => cancel')->default(0);

            $table->tinyInteger('rank_two_status')->comment('0 => pending, 1 => achieved')->default(0);
            $table->tinyInteger('rank_two_claimed_status')->comment('0 => rank not satisfied, 1 => pending (after satisfied rank), 2 => processing,  3 => complete, 4 => cancel')->default(0);

            $table->tinyInteger('rank_three_status')->comment('0 => pending, 1 => achieved')->default(0);
            $table->tinyInteger('rank_three_claimed_status')->comment('0 => rank not satisfied, 1 => pending (after satisfied rank), 2 => processing,  3 => complete, 4 => cancel')->default(0);

            $table->tinyInteger('rank_four_status')->comment('0 => pending, 1 => achieved')->default(0);
            $table->tinyInteger('rank_four_claimed_status')->comment('0 => rank not satisfied, 1 => pending (after satisfied rank), 2 => processing,  3 => complete, 4 => cancel')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claimed_rank_rewards');
    }
};
