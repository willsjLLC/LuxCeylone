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
            $table->bigInteger('package_id')->nullable();
            $table->integer('total_ads')->default(0);
            $table->integer('used_ads')->default(0);
            $table->integer('remaining_ads')->virtualAs('total_ads - used_ads');
            $table->integer('total_boosted_ads')->default(0);
            $table->integer('used_boosted_ads')->default(0);
            $table->integer('remaining_boosted_ads')->virtualAs('total_boosted_ads - used_boosted_ads');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_package_activation_histories', function (Blueprint $table) {
            $table->dropColumn('total_jobs_did');
        });
    }
};
