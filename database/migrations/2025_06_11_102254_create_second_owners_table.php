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
        Schema::create('second_owners', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('original_owner_id')->nullable();
            $table->string('status')->comment('pending => 0, approved => 1, rejected => 2')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('relationship_to_owner')->nullable();
            $table->string('dial_code')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('address')->nullable();
            $table->string('email_address')->nullable();
            $table->string('nic_front_url')->nullable();
            $table->string('nic_back_url')->nullable();
            $table->dateTime(column: 'assigned_date')->nullable();
            $table->dateTime(column: 'approved_date')->nullable();
            $table->timestamp('document_verified_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('second_owners');
    }
};
