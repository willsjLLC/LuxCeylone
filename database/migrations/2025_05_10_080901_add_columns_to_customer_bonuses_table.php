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
        Schema::table('customer_bonuses', function (Blueprint $table) {
            $table->decimal('temp_commission_balance', 15, 2)->after('saving')->default(0);
            $table->decimal('temp_voucher_balance', 15, 2)->after('temp_commission_balance')->default(0);
            $table->decimal('temp_festival_bonus_balance', 15, 2)->after('temp_voucher_balance')->default(0);
            $table->decimal('temp_saving', 15, 2)->after('temp_festival_bonus_balance')->default(0);
            $table->decimal('temp_total', 15, 2)->after('temp_saving')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_bonuses', function (Blueprint $table) {
            //
        });
    }
};
