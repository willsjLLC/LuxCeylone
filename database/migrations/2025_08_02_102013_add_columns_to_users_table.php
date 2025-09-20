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
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('from_pro_account')->default(0)->comment('1 = came from Pro referral, 0 = direct');
            $table->string('pro_referred_username', 255)->nullable()->comment('Username of the Pro account that referred this user');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('from_pro_account');
            $table->dropColumn('pro_referred_username');
            
            
        });
    }
};
