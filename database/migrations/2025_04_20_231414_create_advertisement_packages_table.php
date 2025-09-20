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
        Schema::create('advertisement_packages', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->comment('active = 1, inactive = 0')->nullable();
            $table->String('name')->nullable();
            $table->String('description')->nullable();
            $table->String('package_code')->nullable();
            $table->tinyInteger('type')->comment('default = 0, basic = 1, premium = 2, enterprises = 3')->nullable();
            $table->integer('no_of_advertisements')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('advertisement_duration')->comment('days')->nullable();
            $table->boolean('includes_boost')->default(false);
            $table->integer('no_of_boost')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisement_packages');
    }
};
