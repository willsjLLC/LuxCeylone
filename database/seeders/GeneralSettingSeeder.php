<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralSettingSeeder extends Seeder
{
    public function run()
    {
        DB::table('general_settings')->insert([
            'id' => 1,
            'site_name' => 'Addciti',
            'cur_text' => 'LKR',
            'cur_sym' => 'LKR',
            'email_from' => 'info@viserlab.com',
            'email_from_name' => null,
            'email_template' => '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <!--[if !mso]><!-->
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!--<![endif]-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title></title>
  <style type="text/css">
.ReadMsgBody { width: 100%; background-color: #ffffff; }
.ExternalClass { width: 100%; background-color: #ffffff; }
.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
html { width: 100%; }
body { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; margin: 0; padding: 0; }
table { border-spacing: 0; table-layout: fixed; margin: 0 auto;border-collapse: collapse; }
table table table { table-layout: auto; }
.yshortcuts a { border-bottom: none !important; }
img:hover { opacity: 0.9 !important; }
a { color: #0087ff; text-decoration: none; }
.textbutton a { font-family: \'open sans\', arial, sans-serif !important;}
.btn-link a { color:#FFFFFF !important;}

@media only screen and (max-width: 480px) {
body { width: auto !important; }
*[class="table-inner"] { width: 90% !important; text-align: center !important; }
*[class="table-full"] { width: 100% !important; text-align: center !important; }
/* image */
img[class="img1"] { width: 100% !important; height: auto !important; }
}
</style>



  <table bgcolor="#414a51" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tbody><tr>
      <td height="50"></td>
    </tr>
    <tr>
      <td align="center" style="text-align:center;vertical-align:top;font-size:0;">
        <table align="center" border="0" cellpadding="0" cellspacing="0">
          <tbody><tr>
            <td align="center" width="600">
              <!--header-->
              <table class="table-inner" width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tbody><tr>
                  <td bgcolor="#0087ff" style="border-top-left-radius:6px; border-top-right-radius:6px;text-align:center;vertical-align:top;font-size:0;" align="center">
                    <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
                      <tbody><tr>
                        <td height="20"></td>
                      </tr>
                      <tr>
                        <td align="center" style="font-family: \'Open sans\', Arial, sans-serif; color:#FFFFFF; font-size:16px; font-weight: bold;">This is a System Generated Email</td>
                      </tr>
                      <tr>
                        <td height="20"></td>
                      </tr>
                    </tbody></table>
                  </td>
                </tr>
              </tbody></table>
              <!--end header-->
              <table class="table-inner" width="95%" border="0" cellspacing="0" cellpadding="0">
                <tbody><tr>
                  <td bgcolor="#FFFFFF" align="center" style="text-align:center;vertical-align:top;font-size:0;">
                    <table align="center" width="90%" border="0" cellspacing="0" cellpadding="0">
                      <tbody><tr>
                        <td height="35"></td>
                      </tr>
                      <!--logo-->
                      <tr>
                        <td align="center" style="vertical-align:top;font-size:0;">
                          <a href="#">
                            <img style="display:block; line-height:0px; font-size:0px; border:0px;" src="https://i.imgur.com/Z1qtvtV.png" alt="img">
                          </a>
                        </td>
                      </tr>
                      <!--end logo-->
                      <tr>
                        <td height="40"></td>
                      </tr>
                      <!--headline-->
                      <tr>
                        <td align="center" style="font-family: \'Open Sans\', Arial, sans-serif; font-size: 22px;color:#414a51;font-weight: bold;">Hello {{fullname}} ({{username}})</td>
                      </tr>
                      <!--end headline-->
                      <tr>
                        <td align="center" style="text-align:center;vertical-align:top;font-size:0;">
                          <table width="40" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tbody><tr>
                              <td height="20" style=" border-bottom:3px solid #0087ff;"></td>
                            </tr>
                          </tbody></table>
                        </td>
                      </tr>
                      <tr>
                        <td height="20"></td>
                      </tr>
                      <!--content-->
                      <tr>
                        <td align="left" style="font-family: \'Open sans\', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px;">{{message}}</td>
                      </tr>
                      <!--end content-->
                      <tr>
                        <td height="40"></td>
                      </tr>

                    </tbody></table>
                  </td>
                </tr>
                <tr>
                  <td height="45" align="center" bgcolor="#f4f4f4" style="border-bottom-left-radius:6px;border-bottom-right-radius:6px;">
                    <table align="center" width="90%" border="0" cellspacing="0" cellpadding="0">
                      <tbody><tr>
                        <td height="10"></td>
                      </tr>
                      <!--preference-->
                      <tr>
                        <td class="preference-link" align="center" style="font-family: \'Open sans\', Arial, sans-serif; color:#95a5a6; font-size:14px;">
                          © 2021 <a href="#">{{site_name}}</a>&nbsp;. All Rights Reserved.
                        </td>
                      </tr>
                      <!--end preference-->
                      <tr>
                        <td height="10"></td>
                      </tr>
                    </tbody></table>
                  </td>
                </tr>
              </tbody></table>
            </td>
          </tr>
        </tbody></table>
      </td>
    </tr>
    <tr>
      <td height="60"></td>
    </tr>
  </tbody></table>',
            'sms_template' => 'hi {{fullname}} ({{username}}), {{message}}',
            'sms_from' => 'ViserAdmin',
            'push_title' => null,
            'push_template' => null,
            'base_color' => '009933',
            'mail_config' => '{"name":"php"}',
            'sms_config' => '{"name":"nexmo","clickatell":{"api_key":"----------------"},"infobip":{"username":"------------8888888","password":"-----------------"},"message_bird":{"api_key":"-------------------"},"nexmo":{"api_key":"----------------------","api_secret":"----------------------"},"sms_broadcast":{"username":"----------------------","password":"-----------------------------"},"twilio":{"account_sid":"-----------------------","auth_token":"---------------------------","from":"----------------------"},"text_magic":{"username":"-----------------------","apiv2_key":"-------------------------------"},"custom":{"method":"get","url":"https://hostname/demo-api-v1","headers":{"name":["api_key"],"value":["test_api 555"]},"body":{"name":["from_number"],"value":["5657545757"]}}}',
            'firebase_config' => null,
            'global_shortcodes' => '{
    "site_name":"Name of your site",
    "site_currency":"Currency of your site",
    "currency_symbol":"Symbol of currency"
}',
            'socialite_credentials' => '{"google":{"client_id":"951870834140-fmc2tq14bl23gi9lshsqtc16oub55o62.apps.googleusercontent.com","client_secret":"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","status":0}}',
            'approve_job' => 0,
            'ev' => 0,
            'en' => 1,
            'sv' => 0,
            'sn' => 0,
            'pn' => 0,
            'kv' => 1,
            'multi_language' => 1,
            'force_ssl' => 0,
            'in_app_payment' => 0,
            'maintenance_mode' => 0,
            'secure_password' => 0,
            'agree' => 1,
            'registration' => 1,
            'active_template' => 'basic',
            'system_customized' => 0,
            'paginate_number' => 20,
            'currency_format' => 2,
            'available_version' => '3.0.1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
