<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define categorized permissions
        $permissions = [

            // dashboard permissions
            'dashboard' => [
                'dashboard.view' => 'View Dashboard',
            ],

            // advertisement categories permission list
            'categories' => [
                'categories.view' => 'View Categories',
                'categories.create' => 'Create Categories',
                'categories.update' => 'Update Categories',
                'categories.edit' => 'Edit Categories',
            ],

            // advertisement sub categories permission list
            'sub_categories' => [
                'sub_categories.view' => 'View Sub Categories',
                'sub_categories.create' => 'Create Sub Categories',
                'sub_categories.update' => 'Update Sub Categories',
                'sub_categories.edit' => 'Edit Sub Categories',
            ],

            // file types permission list
            'file_types' => [
                'file_types.view' => 'View File Types',
                'file_types.create' => 'Create File Types',
                'file_types.update' => 'Update File Types'
            ],

            // banner images permission list
            'banner_images' => [
                'banner_images.view' => 'View Products',
                'banner_images.create' => 'Create Products',
                'banner_images.delete' => 'Delete Products',
            ],

            // orders permission list
            'orders' => [
                'orders.view' => 'View Orders',
                'orders.update' => 'Update Orders',
            ],

            // orders permission list
            'orders' => [
                'orders.view' => 'View Orders',
                'orders.update' => 'Update Orders',
            ],

            // product categories permission list
            'product_categories' => [
                'product_categories.view' => 'View Product Categories',
                'product_categories.create' => 'Create Product Categories',
                'product_categories.edit' => 'Edit Product Categories',
                'product_categories.update' => 'Update Product Categories',
            ],

             // product subcategories permission list
            'product_subcategories' => [
                'product_subcategories.view' => 'View Product Sub Categories',
                'product_subcategories.create' => 'Create Product Sub Categories',
                'product_subcategories.edit' => 'Edit Product Sub Categories',
                'product_subcategories.update' => 'Update Product Sub Categories',
            ],

            // company expenses permission list
            'expenses' => [
                'expenses.view' => 'View Company Expenses',
                'expenses.create' => 'Create Company Expenses',
                'expenses.update' => 'Update Company Expenses',
                
            ],

            // product categories permission list
            'products' => [
                'products.view' => 'View Products',
                'products.create' => 'Create Products',
                'products.edit' => 'Edit Products',
                'products.update' => 'Update Products',
            ],

            // promotional banners permission list
            'promotional_banners' => [
                'promotional_banners.view' => 'View Promotional Banners',
                'promotional_banners.create' => 'Create Promotional Banners',
                'promotional_banners.update' => 'Update Promotional Banners',
            ],

            // users permission list
            'users' => [
                'users.view' => 'View All Users',
                'users.update' => 'Update Users',
                'users.login' => 'Login as User',
                'users.detail' => 'View Users Detail',

                'users.leaders' => 'View All Leaders',

                'users.active' => 'View Active Users',
                'users.banned' => 'View Banned Users',
                'users.public' => 'View Public Users',

                'users.email_verified' => 'View Email Verified Users',
                'users.email_unverified' => 'View Email Unverified Users',

                'users.kyc_unverified' => 'View KYC Verified Users',
                'users.kyc_pending' => 'View KYC Pending Users',
                'users.kyc_detail' => 'View KYC User Details',

                'users.mobile_verified' => 'View Mobile Verified Users',
                'users.mobile_unverified' => 'View Mobile Unverified Users',

                'users.with_balance' => 'View Users With balance',

                'users.notification' => 'User Notifications',
            ],

            // deposits permission list
            'deposits' => [
                'deposits.view' => 'View Deposits',
                'deposits.pending' => 'View Pending Deposits',
                'deposits.approved' =>  'View Approved Deposits',
                'deposits.success' => 'View Successful Deposits',
                'deposits.rejected' => 'View Rejected Deposits',
                'deposits.initiated' => 'View Initiated Deposits',
                'deposits.detail' => 'View Deposit Details',
                'deposits.all' => 'View All Deposits',
            ],

            // withdrawals permission list
            'withdrawals' => [
                'withdrawals.view' => 'View Withdrawals Details',
                'withdrawals.pending' => 'View Pending Withdrawals',
                'withdrawals.approved' =>  'View Approved Withdrawals',
                'withdrawals.rejected' => 'View Rejected Withdrawals',
                'withdrawals.all' => 'View All Deposits',
                'withdrawals.update' => 'Update Deposits',
                'withdrawals.detail' => 'View Deposit Details',
            ],

            // reports permission list
            'reports' => [
                'reports.view' => 'View Withdrawals Details',
                'reports.transaction_history' => 'View Transaction History',
                'reports.login_history' =>  'View Login History',
                'reports.notification_history' => 'View Notification History',
                'reports.email_details' => 'View Email Detail History',
                'reports.employee_package_activation_history' => 'View Package Activation History',
                'reports.referral_logs' => 'View Referral Logs',
                'reports.customer_bonus_history' => 'View Customer Bonus History',
                'reports.leader_bonus_history' => 'View Leader Bonus History',
                'reports.top_leader_bonus' => 'View Top Leader Bonus History',
                'reports.company_expenses_saving_history' => 'View Company Expenses Saving History',
                'reports.bonus_transaction_history' => 'View Bonus Transaction History',
                'reports.product_purchase_commission_history' => 'View Product Purchase Commission History',
            ],

            // admin permission list
            'admins' => [
                'admins.view' => 'View Admin Details',
                'admins.edit' => 'Edit Admins',
                'admins.create' => 'Create Admins',
                'admins.update' => 'Update Admins',
            ],

            // permission section access list
            'permissions' => [
                'permissions.view' => 'View Permissions',
                'permissions.update' => 'Update Permissions',
            ],

            // permission system settings
            'settings' => [
                'settings.view' => 'View System Settings',
                'settings.general_settings' => 'Setup General Settings',
                'settings.logo_icon_configurations' => 'Setup Logo and Favicon Configurations',
                'settings.system_configurations' => 'Setup System Configurations',
                'settings.notification_settings' => 'View Notification Settings',
                'settings.payment_gateways' => 'Update Payment Gateways',
                'settings.withdrawal_methods' => 'Update Withdrawal Methods',
                'settings.seo_configurations' => 'Setup SEO Configurations',
                'settings.manage_frontend_&_policy' => 'Manage Frontend',
                'settings.manage_pages' => 'Manage Pages',
                'settings.kyc_settings' => 'KYC Settings Configurations',
                'settings.social_login_settings' => 'Social Login Settings Configurations',
                'settings.language_settings' => 'Language Settings Configurations',
                'settings.extensions_settings' => 'Extensions Configurations',
                'settings.maintain_mode_configurations' => 'Setup Maintain Mode Configurations',
                'settings.cookie_configurations' => 'Setup Cookies Configurations',
                'settings.custom_css_configurations' => 'Setup Custom CSS Configurations',
                'settings.sitemap_configurations' => 'Setup Sitemap Configurations',
                'settings.robot_txt_configurations' => 'Setup Robot txt Configurations',
            ],

            // extra permissions
            'extra' => [
                'extra.cache' => 'Extra Cache Clear',
            ],

            // permission section access list
            'advertisements' => [
                'advertisements.view' => 'View Advertisements',
                'advertisements.update' => 'Update Advertisements',
            ],

            // main packages section access list
            'main_packages' => [
                'main_packages.view' => 'View Main Packages',
                'main_packages.create' => 'Create Main Packages',
                'main_packages.update' => 'Update Main Packages',
                'main_packages.edit' => 'Edit Main Packages',
            ],

            // free packages section access list
            'free_packages' => [
                'free_packages.view' => 'View Free Packages',
                'free_packages.setup' => 'Setup Free Packages',
            ],

            // boost packages section access list
            'boost_packages' => [
                'boost_packages.view' => 'View Boost Packages',
                'boost_packages.create' => 'Create Boost Packages',
                'boost_packages.update' => 'Update Boost Packages',
                'boost_packages.edit' => 'Edit Boost Packages',
            ],

            // training section access list
            'training' => [
                'training.view' => 'View Trainings',
                'training.create' => 'Create Training',
                'training.update' => 'Update Training',
                'training.edit' => 'Edit Training',
            ],

        ];

        foreach ($permissions as $category => $perms) {
            foreach ($perms as $name => $description) {
                Permission::updateOrCreate(
                    ['name' => $name, 'guard_name' => 'admin'],
                    [
                        'category' => $category,
                        'description' => $description,
                    ]
                );
            }
        }


        // Create roles
        // Role::create(['name' => 'super admin', 'guard_name' => 'admin']);
        // Role::create(['name' => 'admin', 'guard_name' => 'admin']);
        // Role::create(['name' => 'sub admin', 'guard_name' => 'admin']);
    }
}
