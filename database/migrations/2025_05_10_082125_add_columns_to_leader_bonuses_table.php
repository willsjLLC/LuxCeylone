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
        Schema::table('leader_bonuses', function (Blueprint $table) {
            $table->decimal('temp_bonus', 12, 2)->after('petrol_allowance')->default(0);
            $table->decimal('temp_leasing_amount', 12, 2)->after('temp_bonus')->default(0);
            $table->decimal('temp_petrol_allowance', 12, 2)->after('temp_leasing_amount')->default(0);
            $table->decimal('temp_total', 15, 2)->after('temp_petrol_allowance')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leader_bonuses', function (Blueprint $table) {
            //
        });
    }
};
