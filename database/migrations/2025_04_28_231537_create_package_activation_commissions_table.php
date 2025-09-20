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
        Schema::create('package_activation_commissions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pkg_id')->nullable();
            $table->decimal('company_commission', 16, 2)->default(0);
            $table->decimal('company_expenses', 16, 2)->default(0);
            $table->decimal('customers_commission', 16, 2)->default(0);
            $table->decimal('customers_voucher', 16, 2)->default(0);
            $table->decimal('customers_festival', 16, 2)->default(0);
            $table->decimal('customers_saving', 16, 2)->default(0);
            $table->decimal('leader_bonus', 16, 2)->default(0);
            $table->decimal('leader_vehicle_lease', 16, 2)->default(0);
            $table->decimal('leader_petrol', 16, 2)->default(0);
            $table->integer('max_ref_complete_to_car')->default(0);
            $table->decimal('top_leader_car', 16, 2)->default(0);
            $table->decimal('top_leader_house', 16, 2)->default(0);
            $table->decimal('top_leader_expenses', 16, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_activation_commissions');
    }
};
