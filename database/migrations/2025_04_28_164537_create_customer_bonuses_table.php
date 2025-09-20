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
        Schema::create('customer_bonuses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('pkg_id')->nullable();
            $table->bigInteger('pkg_activation_comm_id')->nullable();
            $table->boolean('status')->default(false);
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('mobile', 20)->nullable();
            $table->decimal('commission_balance', 15, 2)->default(0);
            $table->decimal('voucher_balance', 15, 2)->default(0);
            $table->decimal('festival_bonus_balance', 15, 2)->default(0);
            $table->decimal('saving', 15, 2)->default(0);
            $table->timestamp('joined_at')->nullable();
            // only this total balance belongs to this table only not affect main balance
            $table->decimal('total_balance', 15, 2)->comment('total bonus balance')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_bonuses');
    }
};
