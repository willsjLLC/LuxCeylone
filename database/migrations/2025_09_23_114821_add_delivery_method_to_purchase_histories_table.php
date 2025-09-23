<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_histories', function (Blueprint $table) {
            $table->string('delivery_method')->nullable()->after('payment_status');
            $table->decimal('delivery_charge', 16, 2)->default(0)->after('delivery_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_histories', function (Blueprint $table) {
            //
        });
    }
};
