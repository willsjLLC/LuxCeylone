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
        Schema::create('company_expenses_saving_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->nullable();
            $table->bigInteger('pkg_id')->nullable();
            $table->bigInteger('pkg_activation_comm_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->string('remark')->nullable();
            $table->decimal('charge', 16, 2)->nullable();
            // Only this total balance belongs to this table only, not affecting main balance
            $table->string('trx_type', 40)->nullable();
            $table->string('trx', 40)->nullable();
            $table->string('details')->nullable();
            $table->decimal('amount', 28, 8)->default(0);
            $table->decimal('post_saving_balance', 28, 8)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_expenses_saving_histories');
    }
};
