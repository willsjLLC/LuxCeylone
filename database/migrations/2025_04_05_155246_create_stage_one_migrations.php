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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40)->nullable();
            $table->string('email', 40)->nullable();
            $table->string('username', 40)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('image')->nullable();
            $table->string('password')->nullable();
            $table->string('remember_token')->nullable();
            $table->timestamps();
        });

        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->default(0);
            $table->string('title')->nullable();
            $table->boolean('is_read')->default(false);
            $table->text('click_url')->nullable();
            $table->timestamps();
        });

        Schema::create('admin_password_resets', function (Blueprint $table) {
            $table->id();
            $table->string('email', 40)->nullable();
            $table->string('token', 40)->nullable();
            $table->boolean('status')->default(true);
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('banner_images', function (Blueprint $table) {
            $table->id();
            $table->string('images')->nullable();;
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id')->nullable();
            $table->bigInteger('product_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('product_name')->nullable();
            $table->decimal('original_price', 10, 2)->nullable();
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('sub_total', 10, 2)->nullable();
            $table->decimal('net_total', 10, 2)->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40)->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('featured')->default(false);
            $table->text('description')->nullable();
            $table->string('image', 40)->nullable();
            $table->timestamps();
        });

        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->default(0);
            $table->bigInteger('method_code')->default(0);
            $table->decimal('amount', 28, 8)->default(0);
            $table->string('method_currency', 40)->nullable();
            $table->decimal('charge', 28, 8)->default(0);
            $table->decimal('rate', 28, 8)->default(0);
            $table->decimal('final_amount', 28, 8)->default(0);
            $table->text('detail')->nullable();
            $table->string('btc_amount')->nullable();
            $table->string('btc_wallet')->nullable();
            $table->string('trx', 40)->nullable();
            $table->integer('payment_try')->default(0);
            $table->tinyInteger('status')->default(0)->comment('1=>success, 2=>pending, 3=>cancel');
            $table->boolean('from_api')->default(false);
            $table->string('admin_feedback')->nullable();
            $table->string('success_url')->nullable();
            $table->string('failed_url')->nullable();
            $table->integer('last_cron')->nullable();
            $table->timestamps();
        });

        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->default(0);
            $table->boolean('is_app')->default(false);
            $table->text('token')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_package_activation_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('payment_method');
            $table->bigInteger('transaction_id')->nullable();
            $table->integer('total_jobs_did');
            $table->timestamps();
            $table->boolean('activation_expired')->default(false);
            $table->string('payment_status')->default('Pending');
            $table->timestamp('expiry_date')->nullable();
        });

        Schema::create('extensions', function (Blueprint $table) {
            $table->id();
            $table->string('act', 40)->nullable();
            $table->string('name', 40)->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->text('script')->nullable();
            $table->text('shortcode')->nullable()->comment('object');
            $table->text('support')->nullable()->comment('help section');
            $table->tinyInteger('status')->default(true)->comment('1=>enable, 2=>disable');
            $table->timestamps();
        });

        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('job_id');
            $table->timestamps();
        });

        Schema::create('favorite_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('product_id');
            $table->timestamps();
        });

        Schema::create('file_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40)->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string('act', 40)->nullable();
            $table->text('form_data')->nullable();
            $table->timestamps();
        });

        // Schema::create('frontends', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('data_keys', 40)->nullable();
        //     $table->longText('data_values')->nullable();
        //     $table->longText('seo_content')->nullable();
        //     $table->string('tempname', 40)->nullable();
        //     $table->string('slug')->nullable();
        //     $table->timestamps();
        // });

        Schema::create('gateways', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('form_id')->default(0);
            $table->integer('code')->nullable();
            $table->string('name', 40)->nullable();
            $table->string('alias', 40)->default('NULL');
            $table->string('image')->nullable();
            $table->tinyInteger('status')->default(true)->comment('1=>enable, 2=>disable');
            $table->text('gateway_parameters')->nullable();
            $table->text('supported_currencies')->nullable();
            $table->tinyInteger('crypto')->default(false)->comment('0: fiat currency, 1: crypto currency');
            $table->text('extra')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gateways');
        Schema::dropIfExists('frontends');
        Schema::dropIfExists('forms');
        Schema::dropIfExists('file_types');
        Schema::dropIfExists('favorite_products');
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('extensions');
        Schema::dropIfExists('employee_package_activation_histories');
        Schema::dropIfExists('device_tokens');
        Schema::dropIfExists('deposits');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('banner_images');
        Schema::dropIfExists('admin_password_resets');
        Schema::dropIfExists('admin_notifications');
        Schema::dropIfExists('admins');
    }
};
