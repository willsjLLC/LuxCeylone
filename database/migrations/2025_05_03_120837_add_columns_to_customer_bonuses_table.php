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
            $table->boolean('is_voucher_open')->after('joined_at')
                ->default(false)
                ->comment('1 = Open, 0 = Closed');

            $table->unsignedInteger('voucher_remaining_to_open')->after('is_voucher_open')
                ->default(0)
                ->comment('Remaining vouchers needed to open');
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
