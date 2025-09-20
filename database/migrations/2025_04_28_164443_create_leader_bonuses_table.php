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
        Schema::create('leader_bonuses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('pkg_activation_comm_id')->nullable();
            $table->bigInteger('pkg_id')->nullable();
            $table->boolean('status')->default(false);
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->decimal('bonus', 12, 2)->default(0);
            $table->decimal('leasing_amount', 12, 2)->default(0);
            $table->decimal('petrol_allowance', 12, 2)->default(0);
            $table->integer('current_referral_count')->comment('single line')->default(0);
            $table->integer('is_progress_completed')->comment('8190, completed => 1,  incomplete  => 0')->default(0);
            $table->timestamp('joined_at')->nullable();
            // only this total balance belongs to this table only not affect main balance
            $table->decimal('total_balance', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leader_bonuses');
    }
};
