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
        Schema::create('gateway_currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40)->nullable();
            $table->string('currency', 40)->nullable();
            $table->string('symbol', 40)->nullable();
            $table->integer('method_code')->nullable();
            $table->string('gateway_alias', 40)->nullable();
            $table->decimal('min_amount', 28, 8)->default(0.00000000);
            $table->decimal('max_amount', 28, 8)->default(0.00000000);
            $table->decimal('percent_charge', 5, 2)->default(0.00);
            $table->decimal('fixed_charge', 28, 8)->default(0.00000000);
            $table->decimal('rate', 28, 8)->default(0.00000000);
            $table->text('gateway_parameter')->nullable();
            $table->timestamps();
        });

        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name', 40)->nullable();
            $table->string('cur_text', 40)->nullable()->comment('currency text');
            $table->string('cur_sym', 40)->nullable()->comment('currency symbol');
            $table->string('email_from', 40)->nullable();
            $table->string('email_from_name')->nullable();
            $table->text('email_template')->nullable();
            $table->string('sms_template')->nullable();
            $table->string('sms_from')->nullable();
            $table->string('push_title')->nullable();
            $table->string('push_template')->nullable();
            $table->string('base_color', 40)->nullable();
            $table->text('mail_config')->nullable()->comment('email configuration');
            $table->text('sms_config')->nullable();
            $table->text('firebase_config')->nullable();
            $table->text('global_shortcodes')->nullable();
            $table->tinyInteger('approve_job')->default(0)->comment('0:off , 1:on');
            $table->tinyInteger('ev')->default(0)->comment('email verification, 0 - dont check, 1 - check');
            $table->tinyInteger('en')->default(0)->comment('email notification, 0 - dont send, 1 - send');
            $table->tinyInteger('sv')->default(0)->comment('mobile verication, 0 - dont check, 1 - check');
            $table->tinyInteger('sn')->default(0)->comment('sms notification, 0 - dont send, 1 - send');
            $table->tinyInteger('pn')->default(1);
            $table->tinyInteger('kv')->default(0);
            $table->tinyInteger('multi_language')->default(0)->comment('0= Language disable, 1 = Language disable\r\n');
            $table->tinyInteger('force_ssl')->default(0);
            $table->tinyInteger('in_app_payment')->default(1);
            $table->tinyInteger('maintenance_mode')->default(0);
            $table->tinyInteger('secure_password')->default(0);
            $table->tinyInteger('agree')->default(0);
            $table->tinyInteger('registration')->default(0)->comment('0: Off  , 1: On');
            $table->string('active_template', 40)->nullable();
            $table->tinyInteger('system_customized')->default(0);
            $table->integer('paginate_number')->default(0);
            $table->tinyInteger('currency_format')->default(0)->comment('1=>Both\r\n2=>Text Only\r\n3=>Symbol Only');
            $table->string('available_version', 40)->default('2');
            $table->timestamps();
        });

        // Schema::create('job_posts', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedInteger('user_id')->default(0);
        //     $table->unsignedInteger('category_id')->default(0);
        //     $table->unsignedInteger('subcategory_id')->default(0);
        //     $table->string('job_code', 40)->nullable();
        //     $table->string('title')->nullable();
        //     $table->text('description')->nullable();
        //     $table->unsignedBigInteger('payment_option_id')->nullable();
        //     $table->tinyInteger('job_proof')->default(0)->comment('1=No Job Prove ,2 =Required job prove\r\n');
        //     $table->string('file_name')->nullable();
        //     $table->integer('quantity')->default(0);
        //     $table->integer('vacancy_available')->default(0);
        //     $table->decimal('rate', 28, 8)->default(0.00000000);
        //     $table->decimal('total', 28, 8)->default(0.00000000);
        //     $table->decimal('amount', 28, 8)->default(0.00000000);
        //     $table->string('attachment')->nullable();
        //     $table->tinyInteger('status')->default(0)->comment('0: pending\r\n1: approve\r\n2: completed\r\n3: pause\r\n9: rejected\r\n');
        //     $table->timestamps();
        // });

        Schema::create('job_proves', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('job_post_id')->default(0);
            $table->text('detail')->nullable();
            $table->string('attachment')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0: pending\r\n1:approved\r\n2:rejected');
            $table->tinyInteger('notification')->default(0);
            $table->timestamps();
            $table->unsignedBigInteger('payment_option_id')->nullable();
            $table->integer('payment_status')->default(0)->nullable();
        });

        Schema::create('key_value_pair', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->text('value');
            $table->timestamps();
        });

        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40)->nullable();
            $table->string('code', 40)->nullable();
            $table->tinyInteger('is_default')->default(0)->comment('0: not default language, 1: default language');
            $table->string('image', 40)->nullable();
            $table->timestamps();
        });

        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->default(0);
            $table->string('sender', 40)->nullable();
            $table->string('sent_from', 40)->nullable();
            $table->string('sent_to', 40)->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->string('notification_type', 40)->nullable();
            $table->string('image')->nullable();
            $table->tinyInteger('user_read')->default(0);
            $table->timestamps();
        });

        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('act', 40)->nullable();
            $table->string('name', 40)->nullable();
            $table->string('subject')->nullable();
            $table->string('push_title')->nullable();
            $table->text('email_body')->nullable();
            $table->text('sms_body')->nullable();
            $table->text('push_body')->nullable();
            $table->text('shortcodes')->nullable();
            $table->tinyInteger('email_status')->default(1);
            $table->string('email_sent_from_name', 40)->nullable();
            $table->string('email_sent_from_address', 40)->nullable();
            $table->tinyInteger('sms_status')->default(1);
            $table->string('sms_sent_from', 40)->nullable();
            $table->tinyInteger('push_status')->default(0);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->default(0);
            $table->string('code')->nullable();
            $table->bigInteger('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('sub_total', 10, 2)->nullable();
            $table->decimal('net_total', 10, 2)->nullable();
            $table->integer('quantity')->nullable();
            $table->string('payment_method')->nullable();
            $table->tinyInteger('payment_status')->default(2);
            $table->string('shipping_address')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('alternative_mobile', 20)->nullable();
            $table->string('zip')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->tinyInteger('delivery_status')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->nullable();
            $table->string('order_code')->nullable();
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
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40)->nullable();
            $table->string('slug', 40)->nullable();
            $table->string('tempname', 40)->nullable()->comment('template name');
            $table->text('secs')->nullable();
            $table->text('seo_content')->nullable();
            $table->tinyInteger('is_default')->default(0);
            $table->timestamps();
        });

        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email', 40)->nullable();
            $table->string('token', 40)->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('payment_option', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('pending_job_commissions', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2);
            $table->text('details')->nullable();
            $table->text('remark')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedTinyInteger('status')->default(0);
            $table->timestamps();
            $table->unsignedBigInteger('send_to_user_id')->nullable();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('product_code')->nullable();
            $table->text('description')->nullable();
            $table->decimal('original_price', 8, 2)->nullable();
            $table->decimal('cost', 10, 2);
            $table->decimal('profit', 10, 2);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('selling_price', 10, 2);
            $table->integer('quantity')->default(0);
            $table->string('unit')->nullable();
            $table->string('sku');
            $table->string('brand')->nullable();
            $table->text('image_url')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->decimal('weight', 8, 2)->nullable();
            $table->bigInteger('user_id');
            $table->bigInteger('category_id');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('products');
        Schema::dropIfExists('pending_job_commissions');
        Schema::dropIfExists('payment_option');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('languages');
        Schema::dropIfExists('key_value_pair');
        Schema::dropIfExists('job_proves');
        Schema::dropIfExists('job_posts');
        Schema::dropIfExists('general_settings');
        Schema::dropIfExists('gateway_currencies');
    }
};
