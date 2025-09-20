<?php

use App\Constants\Status;
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
        Schema::create('product_promotion_banners', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->default(Status::PROMOTION_BANNER_ENABLE);
            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id')->nullable();
            $table->bigInteger('order_id')->nullable();
            $table->bigInteger('transaction_id')->nullable();
            $table->integer('total_purchase_did')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->string('currency', 10)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('referral_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('referrer_id');
            $table->bigInteger('referred_id')->nullable();
          
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity');
        });

        Schema::create('sub_categories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('category_id')->default(0);
            $table->string('name', 40)->nullable();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->string('image', 40)->nullable();
            $table->timestamps();
        });

        Schema::create('support_attachments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('support_message_id')->default(0);
            $table->string('attachment')->nullable();
            $table->timestamps();
        });

        Schema::create('support_messages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('support_ticket_id')->default(0);
            $table->bigInteger('admin_id')->default(0);
            $table->longText('message')->nullable();
            $table->timestamps();
        });

        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->default(0)->nullable();
            $table->string('name', 40)->nullable();
            $table->string('email', 40)->nullable();
            $table->string('ticket', 40)->nullable();
            $table->string('subject')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0: Open, 1: Answered, 2: Replied, 3: Closed');
            $table->tinyInteger('priority')->default(0)->comment('1 = Low, 2 = medium, 3 = heigh');
            $table->dateTime('last_reply')->nullable();
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->default(0);
            $table->decimal('amount', 28, 8)->default(0);
            $table->decimal('charge', 28, 8)->default(0);
            $table->decimal('post_balance', 28, 8)->default(0);
            $table->string('trx_type', 40)->nullable();
            $table->string('trx', 40)->nullable();
            $table->string('details')->nullable();
            $table->string('remark')->nullable();
            $table->timestamps();
        });

        Schema::create('update_logs', function (Blueprint $table) {
            $table->id();
            $table->string('version', 40)->nullable();
            $table->text('update_log')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname', 40)->nullable();
            $table->string('lastname', 40)->nullable();
            $table->string('username', 40)->nullable();
            $table->string('email', 40)->unique();
            $table->string('dial_code', 40)->nullable();
            $table->string('country_code', 40)->nullable();
            $table->string('mobile', 40)->nullable();
            $table->decimal('balance', 28, 8)->default(0);
            $table->string('password');
            $table->bigInteger('referred_user_id')->nullable();
            $table->string('country_name')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('image')->nullable();
            $table->text('address')->nullable()->comment('contains full address');
            $table->boolean('status')->default(true)->comment('0: banned, 1: active');
            $table->boolean('ev')->default(false)->comment('0: email unverified, 1: email verified');
            $table->boolean('sv')->default(false)->comment('0: mobile unverified, 1: mobile verified');
            $table->boolean('profile_complete')->default(false);
            $table->string('ver_code', 40)->nullable()->comment('stores verification code');
            $table->dateTime('ver_code_send_at')->nullable()->comment('verification send time');
            $table->boolean('ts')->default(false)->comment('0: 2fa off, 1: 2fa on');
            $table->boolean('tv')->default(true)->comment('0: 2fa unverified, 1: 2fa verified');
            $table->string('tsc')->nullable();
            $table->tinyInteger('kv')->default(0);
            $table->text('kyc_data')->nullable();
            $table->string('kyc_rejection_reason')->nullable();
            $table->string('ban_reason')->nullable();
            $table->string('remember_token')->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->tinyInteger('employee_package_activated')->default(false);
            $table->decimal('pending_job_commision_total', 15, 2)->default(0);
            $table->timestamps();
            $table->integer('role')->default(1)->comment('1:Customer | 2:Leader');
        });

        Schema::create('user_categories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('category_id');
            $table->bigInteger('sub_category_id')->nullable();
            $table->timestamps();
        });

        Schema::create('user_logins', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->default(0);
            $table->string('user_ip', 40)->nullable();
            $table->string('city', 40)->nullable();
            $table->string('country', 40)->nullable();
            $table->string('country_code', 40)->nullable();
            $table->string('longitude', 40)->nullable();
            $table->string('latitude', 40)->nullable();
            $table->string('browser', 40)->nullable();
            $table->string('os', 40)->nullable();
            $table->timestamps();
        });

        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('method_id')->default(0);
            $table->bigInteger('user_id')->default(0);
            $table->decimal('amount', 28, 8)->default(0);
            $table->string('currency', 40)->nullable();
            $table->decimal('rate', 28, 8)->default(0);
            $table->decimal('charge', 28, 8)->default(0);
            $table->string('trx', 40)->nullable();
            $table->decimal('final_amount', 28, 8)->default(0);
            $table->decimal('after_charge', 28, 8)->default(0);
            $table->text('withdraw_information')->nullable();
            $table->tinyInteger('status')->default(0)->comment('1=>success, 2=>pending, 3=>cancel');
            $table->text('admin_feedback')->nullable();
            $table->timestamps();
        });

        Schema::create('withdraw_methods', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('form_id')->default(0);
            $table->string('name', 40)->nullable();
            $table->string('image')->nullable();
            $table->decimal('min_limit', 28, 8)->default(0)->nullable();
            $table->decimal('max_limit', 28, 8)->default(0);
            $table->decimal('fixed_charge', 28, 8)->default(0)->nullable();
            $table->decimal('rate', 28, 8)->default(0)->nullable();
            $table->decimal('percent_charge', 5, 2)->nullable();
            $table->string('currency', 40)->nullable();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraw_methods');
        Schema::dropIfExists('withdrawals');
        Schema::dropIfExists('user_logins');
        Schema::dropIfExists('user_categories');
        Schema::dropIfExists('users');
        Schema::dropIfExists('update_logs');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('support_messages');
        Schema::dropIfExists('support_attachments');
        Schema::dropIfExists('sub_categories');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('referral_logs');
        Schema::dropIfExists('purchase_histories');
        Schema::dropIfExists('product_promotion_banners');
    }
};
