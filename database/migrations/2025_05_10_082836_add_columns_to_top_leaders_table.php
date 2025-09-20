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
        Schema::table('top_leaders', function (Blueprint $table) {
            $table->decimal('temp_for_car', 15, 2)->after('for_expenses')->default(0);
            $table->decimal('temp_for_house', 15, 2)->after('temp_for_car')->default(0);
            $table->decimal('temp_for_expenses', 15, 2)->after('temp_for_house')->default(0);
            $table->decimal('temp_total', 15, 2)->after('temp_for_expenses')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('top_leaders', function (Blueprint $table) {
            //
        });
    }
};
