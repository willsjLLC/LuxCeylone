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
        Schema::create('top_leaders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('pkg_id')->nullable();
            $table->bigInteger('pkg_activation_comm_id')->nullable();
            $table->bigInteger('leader_id')->nullable();
            $table->decimal('for_car', 15, 2)->default(0);
            $table->decimal('for_house', 15, 2)->default(0);
            $table->decimal('for_expenses', 15, 2)->default(0);
            // only this total balance belongs to this table only not affect main balance
            $table->decimal('total_balance', 15, 2)->comment('total balance from above incomes')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selected_leaders');
    }
};
