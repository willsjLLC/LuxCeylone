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
        Schema::create('bonus_transaction_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->tinyInteger('is_leader')->nullable();
            $table->tinyInteger('is_top_leader')->nullable();
            $table->decimal('amount', 28, 8)->default(0);
            $table->decimal('charge', 28, 8)->default(0);
            $table->string('trx_type', 40)->nullable();
            $table->string('trx', 40)->nullable();
            $table->decimal('customers_voucher', 16, 2)->default(0);
            $table->decimal('customers_festival', 16, 2)->default(0);
            $table->decimal('customers_saving', 16, 2)->default(0);
            $table->decimal('leader_bonus', 16, 2)->default(0);
            $table->decimal('leader_vehicle_lease', 16, 2)->default(0);
            $table->decimal('leader_petrol', 16, 2)->default(0);
            // $table->integer('max_ref_complete_to_car')->default(0);
            $table->decimal('top_leader_car', 16, 2)->default(0);
            $table->decimal('top_leader_house', 16, 2)->default(0);
            $table->decimal('top_leader_expenses', 16, 2)->default(0);
            $table->decimal('post_bonus_balance', 16, 2)->default(0);
            $table->string('details')->nullable();
            $table->string('remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bonus_transaction_histories');
    }
};
