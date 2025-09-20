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
        Schema::table('advertisement_boosted_histories', function (Blueprint $table) {
            $table->bigInteger('transaction_id')->nullable()->after('payment_option_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advertisement_boosted_histories', function (Blueprint $table) {
            //
        });
    }
};
