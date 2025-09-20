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
        Schema::table('admins', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('designation')->nullable()->after('address');
            $table->tinyInteger('status')->after('designation')->comment('active, 1 - ban, 0');;
            $table->timestamp('joined_at')->nullable()->after('status');
            $table->timestamp('last_login_at')->nullable()->after('joined_at');
            $table->foreignId('created_by')->nullable()->after('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin', function (Blueprint $table) {
            //
        });
    }
};
