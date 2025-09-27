<?php

namespace App\Constants;

class FileInfo
{

    /*
    |--------------------------------------------------------------------------
    | File Information
    |--------------------------------------------------------------------------
    |
    | This class basically contain the path of files and size of images.
    | All information are stored as an array. Developer will be able to access
    | this info as method and property using FileManager class.
    |
    */

    public function fileInfo()
    {
        $data['withdrawVerify'] = [
            'path' => 'assets/images/verify/withdraw'
        ];
        $data['depositVerify'] = [
            'path' => 'assets/images/verify/deposit'
        ];
        $data['verify'] = [
            'path' => 'assets/verify'
        ];
        $data['default'] = [
            'path' => 'assets/images/default.png',
        ];
        $data['ticket'] = [
            'path' => 'assets/support',
        ];
        $data['logoIcon'] = [
            'path' => 'assets/user',
        ];
        $data['favicon'] = [
            'size' => '128x128',
        ];
        $data['extensions'] = [
            'path' => 'assets/images/extensions',
            'size' => '36x36',
        ];
        $data['seo'] = [
            'path' => 'assets/images/seo',
            'size' => '1180x600',
        ];
        $data['userProfile'] = [
            'path' => 'assets/images/user/profile',
            'size' => '350x300',
            'default' => 'avatar.jpg'
        ];
        $data['category'] = [
            "path" => 'assets/images/category',
            "size" => '100x100',
        ];
        $data['jobPoster'] = [
            "path" => 'assets/images/job',
            "size" => '600x400',
        ];
        $data['adminProfile'] = [
            'path' => 'assets/admin/images/profile',
            'size' => '400x400',
        ];
        $data['jobProve'] = [
            "path" => 'assets/images/job/prove',
        ];
        $data['subcategory'] = [
            "path" => 'assets/images/subcategory',
            "size" => '100x100',
        ];
        $data['push'] = [
            'path' => 'assets/images/push_notification',
        ];
        $data['appPurchase'] = [
            'path' => 'assets/in_app_purchase_config',
        ];
        $data['maintenance'] = [
            'path' => 'assets/images/maintenance',
            'size' => '660x325',
        ];
        $data['language'] = [
            'path' => 'assets/images/language',
            'size' => '50x50'
        ];
        $data['gateway'] = [
            'path' => 'assets/images/gateway',
            'size' => ''
        ];
        $data['withdrawMethod'] = [
            'path' => 'assets/images/withdraw_method',
            'size' => ''
        ];
        $data['pushConfig'] = [
            'path' => 'assets/admin',
        ];
        $data['productCategory'] = [
            'path' => 'assets/admin/images/product_category',
            'size' => '400x400',
        ];
        $data['product'] = [
            'path' => 'assets/admin/images/product',
            'size' => '400x400',
        ];
        $data['promotionalBanner'] = [
            'path' => 'assets/admin/images/promotionalBanner',
            "size" => '600x400',
        ];
        $data['adminManagement'] = [
            'path' => 'assets/admin/images/adminManagement',
            'size' => '400x400',
        ];
        $data['bannerImage'] = [
            'path' => 'assets/admin/images/bannerImage',
            // 'size'      =>'400x400', 
        ];
        $data['advertisementImages'] = [
            'path' => 'assets/admin/images/advertisementImages',
            'size' => '600x400',
        ];
        $data['secondOwnerImages'] = [
            'path' => 'assets/images/secondOwnerImages',
            // 'size'      =>'600x400', 
        ];
        $data['expenseItems'] = [
            'path' => 'assets/admin/images/expenseItems',
            // 'size'      =>'600x400', 
        ];
        $data['training'] = [
            'path' => 'assets/admin/images/Training',
            'size' => '6000x6000',
        ];
        $data['productSubCategory'] = [
            'path' => 'assets/admin/images/productSubCategory',
            'size' => '600x600',
        ];
        $data['rank'] = [
            'path' => 'assets/admin/images/rank',
            "size" => '400x400',
        ];
        $data['rankReward'] = [
            'path' => 'assets/admin/images/rank/reward',
            "size" => '400x400',
        ];
        return $data;
    }

}
