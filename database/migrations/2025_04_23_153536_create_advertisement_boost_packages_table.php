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
        Schema::create('advertisement_boost_packages', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->comment('active = 1, inactive = 0')->nullable();
            $table->String('name')->nullable();
            $table->String('description')->nullable();
            $table->String('package_code')->nullable();
            $table->integer('duration')->comment('days')->nullable();
            $table->tinyInteger('type')->comment('Top = 1, Featured = 2, Urgent = 3')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('highlighted_color')->nullable();
            $table->tinyInteger('priority_level')->comment('High = 1, Medium = 2, Low = 3')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisement_boost_packages');
    }
};
