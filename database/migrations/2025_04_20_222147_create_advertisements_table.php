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
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('category_id')->nullable(0);
            $table->bigInteger('subcategory_id')->nullable(0);
            $table->bigInteger('package_id')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('advertisement_code', 40)->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('payment_option_id')->nullable();
            $table->string('file_name')->nullable();
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->integer('cpc')->comment('cost per click')->nullable();
            $table->bigInteger('district_id')->nullable();
            $table->bigInteger('city_id')->nullable();
            $table->decimal('price')->default(0);
            $table->decimal('advertisement_cost')->default(0);
            $table->boolean('is_price_negotiable')->nullable();
            $table->string('is_boosted')->nullable();
            $table->dateTime('posted_date')->nullable();
            $table->dateTime('expiry_date')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0: pending\r\n1: approve\r\n2: completed\r\n3: pause\r\n9: rejected\r\n');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
