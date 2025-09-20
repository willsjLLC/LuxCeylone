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
        Schema::table('employee_package_activation_histories', function (Blueprint $table) {
            $table->tinyInteger('can_boost')->nullable()->after('remaining_ads');
            $table->bigInteger('boost_package_id')->nullable()->after('can_boost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_package_activation_histories', function (Blueprint $table) {
            //
        });
    }
};
