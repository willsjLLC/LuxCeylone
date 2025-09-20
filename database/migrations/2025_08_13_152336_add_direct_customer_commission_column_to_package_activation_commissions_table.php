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
        Schema::table('package_activation_commissions', function (Blueprint $table) {
            $table->decimal('level_one_commission',16,2)->after('company_expenses')->default(0);
            $table->decimal('level_two_commission',16,2)->after('level_one_commission')->default(0);
            $table->decimal('level_three_commission',16,2)->after('level_two_commission')->default(0);
            $table->decimal('level_four_commission',16,2)->after('level_three_commission')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_activation_commissions', function (Blueprint $table) {
            $table->dropColumn('customers_commission');
        });
    }
};
