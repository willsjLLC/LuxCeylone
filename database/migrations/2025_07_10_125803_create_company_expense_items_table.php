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
        Schema::create('company_expense_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger(column: 'expense_id')->nullable();
            $table->integer('item_no')->nullable();
            $table->text('description')->nullable();
            $table->decimal('debit', 20,2)->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_expenses_item');
    }
};
