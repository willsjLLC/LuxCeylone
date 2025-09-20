<?php

namespace Database\Seeders;

use App\Models\Frontend;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FrontEndSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $FrontEnds = [
            [
                'id' => 1,
                'data_keys' => 'seo.data',
                'data_values' => [
                    'seo_image' => '1',
                    'keywords' => [
                        'microjobs',
                        'microworker',
                        'small project',
                        'digital marketing',
                        'freelance jobs',
                        'sljob',
                        'sri lanka job',
                        'Sri Lanka jobs',
                        'SL jobs',
                        'job vacancies Sri Lank',
                        'career Sri Lanka',
                        'recruitment Sri Lanka',
                        'government jobs Sri Lanka',
                        'private jobs Sri Lanka',
                        'part-time jobs Sri Lanka',
                        'online jobs Sri Lanka',
                        'work from home Sri Lanka',
                        'job opportunities Sri Lanka',
                        'employment Sri Lanka',
                        'job portal Sri Lanka',
                        'SLJobNet',
                        'find jobs Sri Lanka',
                        'srilanka job'
                    ],
                    'description' => 'Find the latest job opportunities in Sri Lanka with SLJob.Net. Explore government, private, part-time, and online jobs easily!',
                    'social_title' => 'SLJob.Net - Find Your Dream Job in Sri Lanka!',
                    'social_description' => 'Looking for a job in Sri Lanka? SLJob.Net connects you with the latest job vacancies in government, private, part-time, and online sectors. Start your career journey today!',
                    'image' => '679f0b80c17971738476416.png'
                ],
                'seo_content' => NULL,
                'tempname' => 'basic',
                'slug' => '',
            ],
            [
                'id' => 25,
                'data_keys' => 'blog.content',
                'data_values' => [
                    'heading' => 'Latest Blogs',
                    'subheading' => 'Read the latest blogs on SLJob.net for career tips, industry insights, and job market trends'
                ],
                'seo_content' => NULL,
                'tempname' => 'basic',
                'slug' => '',
            ],
            [
                'id' => 27,
                'data_keys' => 'contact_us.content',
                'data_values' => [
                    'title' => 'Get In Touch',
                    'details' => 'Contact us for inquiries, suggestions, or support. The SLJob.Net team is ready to assist you!'
                ],
                'seo_content' => NULL,
                'tempname' => 'basic',
                'slug' => '',
            ],
            [
                'id' => 28,
                'data_keys' => 'counter.content',
                'data_values' => [
                    'heading' => 'Latest News',
                    'sub_heading' => 'Register New Account'
                ],
                'seo_content' => NULL,
                'tempname' => 'basic',
                'slug' => NULL,
            ],
            [
                'id' => 39,
                'data_keys' => 'banner.content',
                'data_values' => [
                    'has_image' => '1',
                    'heading' => 'sljob.net',
                    'subheading' => 'Find the Best Jobs in Our Marketplace.',
                    'description' => 'Find the best job opportunities on SLJob.net, Sri Lanka\'s leading job marketplace connecting employers and job seekers effortlessly!',
                    'button_text' => 'Search',
                    'background_image' => '638ee6782621c1670309496.png',
                    'banner_image' => '638ee678910ad1670309496.png'
                ],
                'seo_content' => NULL,
                'tempname' => 'basic',
                'slug' => '',
            ],
            [
                'id' => 41,
                'data_keys' => 'cookie.data',
                'data_values' => [
                    'short_desc' => 'We may use cookies or any other tracking technologies when you visit our website, including any other media form, mobile website, or mobile application related or connected to help customize the Site and improve your experience.',
                    'description' => '<div class="mb-5" style="color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;">
                                        <h3 class="mb-3" style="font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);">What information do we collect?</h3>
                                        <p class="font-18" style="margin-right: 0px; margin-left: 0px; font-size: 18px !important;">We gather data from you when you register on our site, submit a request, buy any services, react to an overview, or round out a structure. At the point when requesting any assistance or enrolling on our site, as suitable, you might be approached to enter your: name, email address, or telephone number. You may, however, visit our site anonymously.</p>
                                    </div>
                                    <div class="mb-5" style="color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;">
                                        <h3 class="mb-3" style="font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);">How do we protect your information?</h3>
                                        <p class="font-18" style="margin-right: 0px; margin-left: 0px; font-size: 18px !important;">All provided delicate/credit data is sent through Stripe.<br>After an exchange, your private data (credit cards, social security numbers, financials, and so on) won\'t be put away on our workers.</p>
                                    </div>
                                    <div class="mb-5" style="color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;">
                                        <h3 class="mb-3" style="font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);">Do we disclose any information to outside parties?</h3>
                                        <p class="font-18" style="margin-right: 0px; margin-left: 0px; font-size: 18px !important;">We don\'t sell, exchange, or in any case move to outside gatherings by and by recognizable data. This does exclude confided in outsiders who help us in working our site, leading our business, or adjusting you, since those gatherings consent to keep this data private. We may likewise deliver your data when we accept discharge is appropriate to follow the law, implement our site strategies, or ensure our own or others\' rights, property, or wellbeing.</p>
                                    </div>
                                    <div class="mb-5" style="color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;">
                                        <h3 class="mb-3" style="font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);">Children\'s Online Privacy Protection Act Compliance</h3>
                                        <p class="font-18" style="margin-right: 0px; margin-left: 0px; font-size: 18px !important;">We are consistent with the prerequisites of COPPA (Children\'s Online Privacy Protection Act), we don\'t gather any data from anybody under 13 years old. Our site, items, and administrations are completely coordinated to individuals who are in any event 13 years of age or more seasoned.</p>
                                    </div>
                                    <div class="mb-5" style="color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;">
                                        <h3 class="mb-3" style="font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);">Changes to our Privacy Policy</h3>
                                        <p class="font-18" style="margin-right: 0px; margin-left: 0px; font-size: 18px !important;">If we decide to change our privacy policy, we will post those changes on this page.</p>
                                    </div>
                                    <div class="mb-5" style="color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;">
                                        <h3 class="mb-3" style="font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);">How long we retain your information?</h3>
                                        <p class="font-18" style="margin-right: 0px; margin-left: 0px; font-size: 18px !important;">At the point when you register for our site, we cycle and keep your information we have about you however long you don\'t delete the record or unsubscribe yourself (subject to laws and guidelines).</p>
                                    </div>
                                    <div class="mb-5" style="color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;">
                                        <h3 class="mb-3" style="font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);">What we don’t do with your data</h3>
                                        <p class="font-18" style="margin-right: 0px; margin-left: 0px; font-size: 18px !important;">We don\'t and will never share, uncover, sell, or in any case give your information to different organizations for the promoting of their items or administrations.</p>
                                    </div>',
                    'status' => 0,
                ],

                'seo_content' => NULL,
                'tempname' => 'basic',
                'slug' => NULL,
            ],
            [
                'id' => 42,
                'data_keys' => 'policy_pages.element',
                'data_values' => [
                    'title' => 'Privacy Policy',
                    'details' => '<div class="mb-5" style="color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;"><h3 class="mb-3" style="font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);">What information do we collect?</h3><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">We gather data from you when you register on our site, submit a request, buy any services, react to an overview, or round out a structure. At the point when requesting any assistance or enrolling on our site, as suitable, you might be approached to enter your: name, email address, or telephone number. You may, however, visit our site anonymously.</p></div><div class="mb-5" style="color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;"><h3 class="mb-3" style="font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);">How do we protect your information?</h3><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">All provided delicate/credit data is sent through Stripe.<br />After an exchange, your private data (credit cards, social security numbers, financials, and so on) won\'t be put away on our workers.</p></div><div class="mb-5" style="color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;"><h3 class="mb-3" style="font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);">Do we disclose any information to outside parties?</h3><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">We don\'t sell, exchange, or in any case move to outside gatherings by and by recognizable data. This does exclude confided in outsiders who help us in working our site, directing our business, or adjusting you, since those gatherings consent to keep this data private. We may likewise deliver your data when we accept discharge is suitable to follow the law, implement our site strategies, or ensure our own or others\' rights, property, or wellbeing.</p></div><div class="mb-5" style="color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;"><h3 class="mb-3" style="font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);">Children\'s Online Privacy Protection Act Compliance</h3><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">We are consistent with the prerequisites of COPPA (Children\'s Online Privacy Protection Act), we don\'t gather any data from anybody under 13 years old. Our site, items, and administrations are completely coordinated to individuals who are in any event 13 years of age or more seasoned.</p></div><div class="mb-5" style="color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;"><h3 class="mb-3" style="font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);">Changes to our Privacy Policy</h3><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">If we decide to change our privacy policy, we will post those changes on this page.</p></div><div class="mb-5" style="color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;"><h3 class="mb-3" style="font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);">How long we retain your information?</h3><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">At the point when you register for our site, we cycle and keep your information we have about you however long you don\'t erase the record or withdraw yourself (subject to laws and guidelines).</p></div><div class="mb-5" style="color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;"><h3 class="mb-3" style="font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);">What we don’t do with your data</h3><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">We don\'t and will never share, disclose, sell, or in any case give your information to different organizations for the promoting of their items or administrations.</p></div>',
                ],
                'seo_content' => NULL,
                'tempname' => 'basic',
                'slug' => 'privacy-policy',
            ],
            [
                'id' => 43,
                'data_keys' => 'policy_pages.element',
                'data_values' => [
                    'title' => 'Terms of Service',
                    'details' => '<div class="mb-5" style="color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;"><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">We claim all authority to dismiss, end, or handicap any help with or without cause per administrator discretion. This is a Complete independent facilitating, on the off chance that you misuse our ticket or Livechat or emotionally supportive network by submitting solicitations or protests we will impair your record. The solitary time you should reach us about the seaward facilitating is if there is an issue with the worker. We have not many substance limitations and everything is as per laws and guidelines. Try not to join on the off chance that you intend to do anything contrary to the guidelines, we do check these things and we will know, don\'t burn through our own and your time by joining on the off chance that you figure you will have the option to sneak by us and break the terms.</p></div><div class="mb-5" style="color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;"><ul class="font-18" style="padding-left:15px;list-style-type:disc;font-size:18px;"><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Configuration requests - If you have a fully managed dedicated server with us then we offer custom PHP/MySQL configurations, firewalls for dedicated IPs, DNS, and httpd configurations.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Software requests - Cpanel Extension Installation will be granted as long as it does not interfere with the security, stability, and performance of other users on the server.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Emergency Support - We do not provide emergency support / Phone Support / LiveChat Support. Support may take some hours sometimes.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Webmaster help - We do not offer any support for webmaster related issues and difficulty including coding, &amp; installs, Error solving. if there is an issue where a library or configuration of the server then we can help you if it\'s possible from our end.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Backups - We keep backups but we are not responsible for data loss, you are fully responsible for all backups.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">We Don\'t support any child porn or such material.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">No spam-related sites or material, such as email lists, mass mail programs, and scripts, etc.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">No harassing material that may cause people to retaliate against you.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">No phishing pages.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">You may not run any exploitation script from the server. reason can be terminated immediately.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">If Anyone attempting to hack or exploit the server by using your script or hosting, we will terminate your account to keep safe other users.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Malicious Botnets are strictly forbidden.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Spam, mass mailing, or email marketing in any way are strictly forbidden here.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Malicious hacking materials, trojans, viruses, &amp; malicious bots running or for download are forbidden.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Resource and cronjob abuse is forbidden and will result in suspension or termination.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Php/CGI proxies are strictly forbidden.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">CGI-IRC is strictly forbidden.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">No fake or disposal mailers, mass mailing, mail bombers, SMS bombers, etc.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">NO CREDIT OR REFUND will be granted for interruptions of service, due to User Agreement violations.</li></ul></div><div class="mb-5" style="color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;"><h3 class="mb-3" style="font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);">Terms &amp; Conditions for Users</h3><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">Before getting to this site, you are consenting to be limited by these site Terms and Conditions of Use, every single applicable law, and regulations, and concur that you are answerable for consistency with any material neighborhood laws. If you disagree with any of these terms, you are restricted from utilizing or getting to this site.</p></div><div class="mb-5" style="color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;"><h3 class="mb-3" style="font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);">Support</h3><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">Whenever you have downloaded our item, you may get in touch with us for help through email and we will give a valiant effort to determine your issue. We will attempt to answer using the Email for more modest bug fixes, after which we will refresh the center bundle. Content help is offered to confirmed clients by Tickets as it were. Backing demands made by email and Livechat.</p><p class="my-3 font-18 font-weight-bold" style="margin-right:0px;margin-left:0px;font-size:18px;">On the off chance that your help requires extra adjustment of the System, at that point, you have two alternatives:</p><ul class="font-18" style="padding-left:15px;list-style-type:disc;font-size:18px;"><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Hang tight for additional update discharge.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Or on the other hand, enlist a specialist (We offer customization for extra charges).</li></ul></div><div class="mb-5" style="color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;"><h3 class="mb-3" style="font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);">Ownership</h3><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">You may not claim scholarly or selective possession of any of our items, altered or unmodified. All items are property, we created them. Our items are given "with no guarantees" without warranty of any kind, either communicated or implied. On no occasion will our juridical individual be subject to any harms including, however not limited to, direct, roundabout, extraordinary, incidental, or significant harms or different misfortunes arising out of the utilization of or powerlessness to utilize our items.</p></div><div class="mb-5" style="color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;"><h3 class="mb-3" style="font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);">Warranty</h3><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">We don\'t offer any guarantee or assurance of these Services in any way. When our Services have been modified we can\'t ensure they will work with all outsider plugins, modules, or internet browsers. Program similarity ought to be tried against the show formats on the demo worker. If you don\'t mind guarantee that the programs you use will work with the component, as we can not guarantee that our systems will work with all program mixes.</p></div><div class="mb-5" style="color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;"><h3 class="mb-3" style="font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);">Unauthorized/Illegal Usage</h3><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">You may not utilize our things for any illicit or unapproved reason or may you, in the utilization of the stage, disregard any laws in your locale (counting yet not limited to copyright laws) just as the laws of your nation and International law. In particular, it is prohibited to utilize the things on our foundation for pages that advance: violence, illegal intimidation, hard sexual entertainment, racism, obscenity content or warez programming joins.<br /><br />You can\'t imitate, copy, duplicate, sell, exchange or adventure any of our component, utilization of the offered on our things, or admittance to the administration without the express composed consent by us or item proprietor.<br /><br />Our Members are liable for all substance posted on the discussion and demo and movement that happens under your record.<br /><br />We hold the chance of hindering your participation account immediately if we will think about a particularly not allowed conduct.<br /><br />If you make a record on our site, you are liable for keeping up the security of your account, and you are completely answerable for all exercises that happen under the record and some other actions taken regarding the record. You should immediately inform us, of any unapproved employments of your record or some other breaches of security.</p></div><div class="mb-5" style="color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;"><h3 class="mb-3" style="font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);">Fiverr, Seoclerks Sellers Or Affiliates</h3><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">We do NOT guarantee full SEO campaign conveyance within 24 hours. We make no assurance for conveyance time by any means. We give our best assessment to orders during the putting in of requests, anyway, these are gauges. We won\'t be considered liable for loss of assets, negative reviews or you being prohibited for late conveyance. If you are selling on a site that requires time touchy outcomes, utilize Our SEO Services at your own risk.</p></div><div class="mb-5" style="color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;"><h3 class="mb-3" style="font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);">Payment/Refund Policy</h3><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">No refund or cash back will be made. After a deposit has been finished, it is extremely unlikely to invert it. You should utilize your equilibrium on requests our administrations, Hosting, SEO campaign. You concur that once you complete a deposit, you won\'t document a debate or a chargeback against us in any way, shape, or form.<br /><br />If you document a debate or chargeback against us after a deposit, we claim all authority to end every single future request, prohibit you from our site. False action, for example, utilizing unapproved or taken charge cards will prompt the end of your record. There are no special cases.</p></div><div class="mb-5" style="color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;"><h3 class="mb-3" style="font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);">Free Balance / Coupon Policy</h3><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">We offer numerous approaches to get FREE Balance, Coupons and Deposit offers yet we generally reserve the privilege to audit it and deduct it from your record offset with any explanation we may it is a sort of misuse. If we choose to deduct a few or all of free Balance from your record balance, and your record balance becomes negative, at that point the record will automatically be suspended. If your record is suspended because of a negative Balance you can request to make a custom payment to settle your equilibrium to actuate your record.</p></div>',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => 'terms-of-service',
            ],
            [
                'id' => 44,
                'data_keys' => 'maintenance.data',
                'data_values' => [
                    'description' => '<div class="mb-5" style="color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;"><h2 style="font-family: Poppins, sans-serif; text-align: center;"><font size="6">We\'re just tuning up a few things.</font></h2><h3 class="mb-3" style="text-align: center; font-weight: 600; line-height: 1.3; font-family: Exo, sans-serif; color: rgb(54, 54, 54);"><p style="text-align: center; font-family: Poppins, sans-serif;"><font size="4">We apologize for the inconvenience but Front is currently undergoing planned maintenance. Thanks for your patience.</font></p></h3></div>',
                    'image' => '667d56590a6491719490137.png',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => null,
            ],
            [
                'id' => 50,
                'data_keys' => 'blog.element',
                'data_values' => [
                    'has_image' => ['1'],
                    'title' => 'Your next job starts right here',
                    'description' => '<p style="margin:0px 0px 15px;color:rgb(119,136,153);font-size:16px;line-height:1.7;font-style:normal;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;padding:0px;font-family:\'Work Sans\', sans-serif;background-color:rgb(255,255,255);"><font size="5">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</font></p><p style="margin:0px 0px 15px;color:rgb(119,136,153);font-size:16px;line-height:1.7;font-weight:400;font-style:normal;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;padding:0px;font-family:\'Work Sans\', sans-serif;background-color:rgb(255,255,255);">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p><p style="margin:0px;color:rgb(119,136,153);font-size:16px;line-height:1.7;font-weight:400;font-style:normal;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;padding:0px;font-family:\'Work Sans\', sans-serif;background-color:rgb(255,255,255);">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.</p>',
                    'image' => '638eeaed7c2f31670310637.jpg',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => 'your-next-job-starts-right-here',
            ],
            [
                'id' => 51,
                'data_keys' => 'blog.element',
                'data_values' => [
                    'has_image' => ['1'],
                    'title' => 'There ara many variationsof passages',
                    'description' => '<p style="margin:0px 0px 15px;color:rgb(119,136,153);font-size:16px;line-height:1.7;font-weight:400;font-style:normal;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;padding:0px;font-family:\'Work Sans\', sans-serif;background-color:rgb(255,255,255);">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p><p style="margin:0px 0px 15px;color:rgb(119,136,153);font-size:16px;line-height:1.7;font-weight:400;font-style:normal;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;padding:0px;font-family:\'Work Sans\', sans-serif;background-color:rgb(255,255,255);">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p><p style="margin:0px;color:rgb(119,136,153);font-size:16px;line-height:1.7;font-weight:400;font-style:normal;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;padding:0px;font-family:\'Work Sans\', sans-serif;background-color:rgb(255,255,255);">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.</p>',
                    'image' => '638eeb0f0d3141670310671.jpg',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => 'there-ara-many-variationsof-passages',
            ],
            [
                'id' => 52,
                'data_keys' => 'blog.element',
                'data_values' => [
                    'has_image' => ['1'],
                    'title' => 'People who  completed NAND technology got job',
                    'description' => '<p style="margin:0px 0px 15px;color:rgb(119,136,153);font-size:16px;line-height:1.7;font-weight:400;font-style:normal;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;padding:0px;font-family:\'Work Sans\', sans-serif;background-color:rgb(255,255,255);">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p><p style="margin:0px 0px 15px;color:rgb(119,136,153);font-size:16px;line-height:1.7;font-weight:400;font-style:normal;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;padding:0px;font-family:\'Work Sans\', sans-serif;background-color:rgb(255,255,255);">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p><p style="margin:0px;color:rgb(119,136,153);font-size:16px;line-height:1.7;font-weight:400;font-style:normal;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;padding:0px;font-family:\'Work Sans\', sans-serif;background-color:rgb(255,255,255);">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.</p>',
                    'image' => '638eeb29c439a1670310697.jpg',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => 'people-who--completed-nand-technology-got-job',
            ],
            [
                'id' => 53,
                'data_keys' => 'blog.element',
                'data_values' => [
                    'has_image' => ['1'],
                    'title' => 'Kofejob -How to get job through online...',
                    'description' => '<p style="margin:0px 0px 15px;color:rgb(119,136,153);font-size:16px;line-height:1.7;font-weight:400;font-style:normal;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;padding:0px;font-family:\'Work Sans\', sans-serif;background-color:rgb(255,255,255);">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p><p style="margin:0px 0px 15px;color:rgb(119,136,153);font-size:16px;line-height:1.7;font-weight:400;font-style:normal;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;padding:0px;font-family:\'Work Sans\', sans-serif;background-color:rgb(255,255,255);">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p><p style="margin:0px;color:rgb(119,136,153);font-size:16px;line-height:1.7;font-weight:400;font-style:normal;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;padding:0px;font-family:\'Work Sans\', sans-serif;background-color:rgb(255,255,255);">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.</p>',
                    'image' => '638eeb41f0bec1670310721.jpg',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => 'kofejob--how-to-get-job-through-online',
            ],
            [
                'id' => 54,
                'data_keys' => 'blog.element',
                'data_values' => [
                    'has_image' => ['1'],
                    'title' => 'placeat facere possimus, omnis voluptas assumenda est',
                    'description' => '<p style="margin:0px 0px 15px;color:rgb(119,136,153);font-size:16px;line-height:1.7;font-weight:400;font-style:normal;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;padding:0px;font-family:\'Work Sans\', sans-serif;background-color:rgb(255,255,255);">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p><p style="margin:0px 0px 15px;color:rgb(119,136,153);font-size:16px;line-height:1.7;font-weight:400;font-style:normal;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;padding:0px;font-family:\'Work Sans\', sans-serif;background-color:rgb(255,255,255);">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p><p style="margin:0px;color:rgb(119,136,153);font-size:16px;line-height:1.7;font-weight:400;font-style:normal;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;padding:0px;font-family:\'Work Sans\', sans-serif;background-color:rgb(255,255,255);">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.</p>',
                    'image' => '638eeb64dea041670310756.jpg',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => 'placeat-facere-possimus-omnis-voluptas-assumenda-est',
            ],
            [
                'id' => 55,
                'data_keys' => 'blog.element',
                'data_values' => [
                    'has_image' => ['1'],
                    'title' => 'voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaeca',
                    'description' => '<p style="margin:0px 0px 15px;color:rgb(119,136,153);font-size:16px;line-height:1.7;font-weight:400;font-style:normal;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;padding:0px;font-family:\'Work Sans\', sans-serif;background-color:rgb(255,255,255);">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p><p style="margin:0px 0px 15px;color:rgb(119,136,153);font-size:16px;line-height:1.7;font-weight:400;font-style:normal;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;padding:0px;font-family:\'Work Sans\', sans-serif;background-color:rgb(255,255,255);">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p><p style="margin:0px;color:rgb(119,136,153);font-size:16px;line-height:1.7;font-weight:400;font-style:normal;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;padding:0px;font-family:\'Work Sans\', sans-serif;background-color:rgb(255,255,255);">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.</p>',
                    'image' => '638eeb82a9b881670310786.jpg',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => 'voluptate-velit-esse-cillum-dolore-eu-fugiat-nulla-pariatur-excepteur-sint-occaeca',
            ],
            [
                'id' => 56,
                'data_keys' => 'faq.element',
                'data_values' => [
                    'has_image' => ['1'],
                    'title' => 'placeat facere possimus, omnis voluptas assumenda est',
                    'description' => '<p style="margin:0px 0px 15px;color:rgb(119,136,153);font-size:16px;line-height:1.7;font-weight:400;font-style:normal;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;padding:0px;font-family:\'Work Sans\', sans-serif;background-color:rgb(255,255,255);">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p><p style="margin:0px 0px 15px;color:rgb(119,136,153);font-size:16px;line-height:1.7;font-weight:400;font-style:normal;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;padding:0px;font-family:\'Work Sans\', sans-serif;background-color:rgb(255,255,255);">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p><p style="margin:0px;color:rgb(119,136,153);font-size:16px;line-height:1.7;font-weight:400;font-style:normal;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;padding:0px;font-family:\'Work Sans\', sans-serif;background-color:rgb(255,255,255);">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.</p>',
                    'image' => '638eeb64dea041670310756.jpg',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => null,
            ],
            [
                'id' => 57,
                'data_keys' => 'faq.element',
                'data_values' => [
                    'question' => 'These predominantly rely on the pre-results (Google Answers and Featured',
                    'answer' => '<span style="color:rgb(119,136,153);font-family:\'Work Sans\', sans-serif;font-size:16px;font-style:normal;font-weight:400;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;background-color:rgb(255,255,255);float:none;">In no small part, the importance of FAQ pages has been driven in recent years by the growth in voice search, mobile search, and personal/home assistants and speakers.These predominantly rely on the pre-results (Google Answers and Featured Snippets) and can be targeted specifically with FAQ page</span>',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => null,
            ],
            [
                'id' => 58,
                'data_keys' => 'faq.element',
                'data_values' => [
                    'question' => 'Lands new users to the website by solving problems..',
                    'answer' => '<span style="color:rgb(119,136,153);font-family:\'Work Sans\', sans-serif;font-size:16px;font-style:normal;font-weight:400;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;background-color:rgb(255,255,255);float:none;">In no small part, the importance of FAQ pages has been driven in recent years by the growth in voice search, mobile search, and personal/home assistants and speakers.These predominantly rely on the pre-results (Google Answers and Featured Snippets) and can be targeted specifically with FAQ page</span>',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => null,
            ],
            [
                'id' => 59,
                'data_keys' => 'faq.element',
                'data_values' => [
                    'question' => 'Showcases expertise, trust, and authority within your niche..',
                    'answer' => '<span style="color:rgb(119,136,153);font-family:\'Work Sans\', sans-serif;font-size:16px;font-style:normal;font-weight:400;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;background-color:rgb(255,255,255);float:none;">In no small part, the importance of FAQ pages has been driven in recent years by the growth in voice search, mobile search, and personal/home assistants and speakers.These predominantly rely on the pre-results (Google Answers and Featured Snippets) and can be targeted specifically with FAQ page</span>',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => null,
            ],
            [
                'id' => 60,
                'data_keys' => 'faq.element',
                'data_values' => [
                    'question' => 'Lands new users to the website by solving problems..',
                    'answer' => '<p style="margin:0px 0px 15px;color:rgb(119,136,153);font-size:16px;line-height:1.7;font-weight:400;font-style:normal;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;background-color:rgb(255,255,255);padding:0px;font-family:\'Work Sans\', sans-serif;">In no small part, the importance of FAQ pages has been driven in recent years by the growth in voice search, mobile search, and personal/home assistants and speakers.These predominantly rely on the pre-results (Google Answers and Featured Snippets) and can be targeted specifically with FAQ page Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptates, inventore reprehenderit. Fuga, quod facilis aspernatur distinctio ipsum libero modi esse sequi, eum dicta sapiente rem consequatur minus quos laboriosam quisquam.</p><p style="margin:0px 0px 15px;color:rgb(119,136,153);font-size:16px;line-height:1.7;font-weight:400;font-style:normal;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;background-color:rgb(255,255,255);padding:0px;font-family:\'Work Sans\', sans-serif;">Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolorem labore quisquam minima repellat officiis voluptas, quod laudantium doloremque atque quae earum, beatae iusto numquam! Eligendi quam ullam molestiae reiciendis eos!</p>',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => null,
            ],
            [
                'id' => 61,
                'data_keys' => 'faq.element',
                'data_values' => [
                    'question' => 'These predominantly rely on the pre-results (Google Answers and Featured',
                    'answer' => '<span style="color:rgb(119,136,153);font-family:\'Work Sans\', sans-serif;font-size:16px;font-style:normal;font-weight:400;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;background-color:rgb(255,255,255);float:none;">In no small part, the importance of FAQ pages has been driven in recent years by the growth in voice search, mobile search, and personal/home assistants and speakers.These predominantly rely on the pre-results (Google Answers and Featured Snippets) and can be targeted specifically with FAQ page</span>',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => null,
            ],
            [
                'id' => 62,
                'data_keys' => 'faq.element',
                'data_values' => [
                    'question' => 'Frequently gets updated based on new data insights.',
                    'answer' => '<span style="color:rgb(119,136,153);font-family:\'Work Sans\', sans-serif;font-size:16px;font-style:normal;font-weight:400;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;background-color:rgb(255,255,255);float:none;">In no small part, the importance of FAQ pages has been driven in recent years by the growth in voice search, mobile search, and personal/home assistants and speakers.These predominantly rely on the pre-results (Google Answers and Featured Snippets) and can be targeted specifically with FAQ page</span>',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => null,
            ],
            [
                'id' => 63,
                'data_keys' => 'faq.element',
                'data_values' => [
                    'question' => 'Lands new users to the website by solving problems..',
                    'answer' => '<span style="color:rgb(119,136,153);font-family:\'Work Sans\', sans-serif;font-size:16px;font-style:normal;font-weight:400;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;background-color:rgb(255,255,255);float:none;">In no small part, the importance of FAQ pages has been driven in recent years by the growth in voice search, mobile search, and personal/home assistants and speakers.These predominantly rely on the pre-results (Google Answers and Featured Snippets) and can be targeted specifically with FAQ page</span>',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => null,
            ],
            [
                'id' => 64,
                'data_keys' => 'faq.element',
                'data_values' => [
                    'question' => 'Showcases expertise, trust, and authority within your niche..',
                    'answer' => '<span style="color:rgb(119,136,153);font-family:\'Work Sans\', sans-serif;font-size:16px;font-style:normal;font-weight:400;letter-spacing:normal;text-align:left;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;background-color:rgb(255,255,255);float:none;">In no small part, the importance of FAQ pages has been driven in recent years by the growth in voice search, mobile search, and personal/home assistants and speakers.These predominantly rely on the pre-results (Google Answers and Featured Snippets) and can be targeted specifically with FAQ page</span>',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => null,
            ],
            [
                'id' => 65,
                'data_keys' => 'breadcrumb.content',
                'data_values' => [
                    'has_image' => '1',
                    'image' => '638eee7e81c1f1670311550.png',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => null,
            ],
            [
                'id' => 66,
                'data_keys' => 'social_icon.element',
                'data_values' => [
                    'title' => 'Facebook',
                    'icon' => '<i class="lab la-facebook-f"></i>',
                    'url' => 'https://www.facebook.com/',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => null,
            ],
            [
                'id' => 67,
                'data_keys' => 'social_icon.element',
                'data_values' => [
                    'title' => 'Twitter',
                    'icon' => '<i class="lab la-twitter"></i>',
                    'url' => 'https://twitter.com/',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => null,
            ],
            [
                'id' => 68,
                'data_keys' => 'social_icon.element',
                'data_values' => [
                    'title' => 'Pinterest',
                    'icon' => '<i class="lab la-pinterest-p"></i>',
                    'url' => 'https://www.pinterest.com/',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => null,
            ],
            [
                'id' => 69,
                'data_keys' => 'social_icon.element',
                'data_values' => [
                    'title' => 'Linkedin',
                    'icon' => '<i class="lab la-linkedin-in"></i>',
                    'url' => 'https://www.linkedin.com/',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => null,
            ],
            [
                'id' => 70,
                'data_keys' => 'footer.content',
                'data_values' => [
                    'has_image' => '1',
                    'description' => 'SLJob.net – Your trusted job marketplace in Sri Lanka. Find your dream job or the perfect candidate with ease. Stay connected with us for the latest job updates. Your career starts here!',
                    'image' => '638ef012da3591670311954.jpg',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => '',
            ],
            [
                'id' => 71,
                'data_keys' => 'counter.element',
                'data_values' => [
                    'title' => 'TOTAL JOB POSTS',
                    'digit' => '175',
                    'digit_postfix' => 'K+',
                    'icon' => '<i class="fas fa-briefcase"></i>',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => null,
            ],
            [
                'id' => 72,
                'data_keys' => 'counter.element',
                'data_values' => [
                    'title' => 'COMPLETED PROJECTS',
                    'digit' => '500',
                    'digit_postfix' => 'M+',
                    'icon' => '<i class="las la-tasks"></i>',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => null,
            ],
            [
                'id' => 73,
                'data_keys' => 'counter.element',
                'data_values' => [
                    'title' => 'REGISTERED FREELANCERS',
                    'digit' => '150',
                    'digit_postfix' => 'K+',
                    'icon' => '<i class="las la-user-tie"></i>',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => null,
            ],
            [
                'id' => 74,
                'data_keys' => 'contact_us.element',
                'data_values' => [
                    'title' => 'Phone',
                    'content' => '+94 719 202020',
                    'attribute' => 'tel:',
                    'icon' => '<i class="las la-phone-volume"></i>',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => '',
            ],
            [
                'id' => 75,
                'data_keys' => 'contact_us.element',
                'data_values' => [
                    'title' => 'Email',
                    'content' => 'contact@sljob.net',
                    'attribute' => 'mailto:',
                    'icon' => '<i class="lar la-envelope-open"></i>',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => '',
            ],
            [
                'id' => 76,
                'data_keys' => 'contact_us.element',
                'data_values' => [
                    'title' => 'Location',
                    'content' => 'No 1493/1A, Hokandara road, Kottawa north, Pannipitiya, Sri Lanka',
                    'attribute' => 'mailto:',
                    'icon' => '<i class="las la-map-marker"></i>',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => '',
            ],
            [
                'id' => 77,
                'data_keys' => 'overview.content',
                'data_values' => [
                    'has_image' => '1',
                    'heading_left' => 'I Want To Show my Talent Here',
                    'subheading_left' => 'Find your jobs by Searching related your job title. Or browse job by Category as per your need and profession',
                    'left_button_text' => 'Find A Job',
                    'left_button_link' => 'job/list',
                    'heading_right' => 'I Want To Hire Freelancer for my Project',
                    'subheading_right' => 'Find your jobs by Searching related your job title. Or browse job by Category as per your need and profession',
                    'right_button_text' => 'Post A Job',
                    'right_button_link' => 'user/job/create',
                    'background_image' => '638f07053892a1670317829.png',
                    'overview_image' => '638f07058bec91670317829.png',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => null,
            ],
            [
                'id' => 78,
                'data_keys' => 'overview.element',
                'data_values' => [
                    'heading' => 'I Want To Show my Talent Here',
                    'subheading' => 'Find your jobs by Searching related your job title. Or browse job by Category as per your need and profession',
                    'button_text' => 'Find A Job',
                    'button_link' => 'job/list',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => null,
            ],
            [
                'id' => 79,
                'data_keys' => 'overview.element',
                'data_values' => [
                    'heading' => 'I Want To Hire Freelancer for my Project',
                    'subheading' => 'Find your jobs by Searching related your job title. Or browse job by Category as per your need and profession',
                    'button_text' => 'Post A Job',
                    'button_link' => 'user/job/create',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => null,
            ],
            [
                'id' => 80,
                'data_keys' => 'get_started.content',
                'data_values' => [
                    'heading' => 'Let\'s Get Started',
                    'subheading' => 'Let\'s Get Started – Explore top job opportunities on SLJob.net and take the next step in your career today!',
                    'button_text' => 'Get Started Now',
                    'button_link' => 'user/register',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => '',
            ],
            [
                'id' => 83,
                'data_keys' => 'top_freelancer.content',
                'data_values' => [
                    'heading' => 'Most Job Completed  Freelancers',
                    'subheading' => 'Discover the top freelancers with the most completed jobs on SLJob.net – Hire the best talent for your projects!',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => '',
            ],
            [
                'id' => 84,
                'data_keys' => 'job_post.content',
                'data_values' => [
                    'heading' => 'Latest Job Posts',
                    'subheading' => 'Stay updated with the latest job posts on SLJob.net and find the perfect opportunity for your career!',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => '',
            ],
            [
                'id' => 85,
                'data_keys' => 'job_category.content',
                'data_values' => [
                    'heading' => 'Find Your Jobs Easily',
                    'subheading' => 'Find your dream job quickly and easily with SLJob.net, Sri Lanka\'s leading job marketplace!',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => '',
            ],
            [
                'id' => 86,
                'data_keys' => 'register.content',
                'data_values' => [
                    'has_image' => '1',
                    'heading' => 'Sign Up Account',
                    'subheading' => 'Please fill all the required form to create an account in sljob.net',
                    'image' => '63919e35cdfd31670487605.jpg',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => '',
            ],
            [
                'id' => 87,
                'data_keys' => 'login.content',
                'data_values' => [
                    'has_image' => '1',
                    'heading' => 'Log in Account',
                    'subheading' => 'Please log in your account to access your all data.',
                    'image' => '6391a07bb43d91670488187.jpg',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => null,
            ],
            [
                'id' => 88,
                'data_keys' => 'policy_pages.element',
                'data_values' => [
                    'title' => 'Refund Policy',
                    'details' => '<div class="mb-5" style="margin-bottom:3rem;color:rgb(111,111,111);font-family:Nunito, sans-serif;"><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">We claim all authority to dismiss, end, or handicap any help with or without cause per administrator discretion. This is a Complete independent facilitating, on the off chance that you misuse our ticket or Livechat or emotionally supportive network by submitting solicitations or protests we will impair your record. The solitary time you should reach us about the seaward facilitating is if there is an issue with the worker. We have not many substance limitations and everything is as per laws and guidelines. Try not to join on the off chance that you intend to do anything contrary to the guidelines, we do check these things and we will know, don\'t burn through our own and your time by joining on the off chance that you figure you will have the option to sneak by us and break the terms.</p></div><div class="mb-5" style="margin-bottom:3rem;color:rgb(111,111,111);font-family:Nunito, sans-serif;"><ul class="font-18" style="padding-left:15px;list-style-type:disc;font-size:18px;"><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Configuration requests - If you have a fully managed dedicated server with us then we offer custom PHP/MySQL configurations, firewalls for dedicated IPs, DNS, and httpd configurations.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Software requests - Cpanel Extension Installation will be granted as long as it does not interfere with the security, stability, and performance of other users on the server.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Emergency Support - We do not provide emergency support / Phone Support / LiveChat Support. Support may take some hours sometimes.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Webmaster help - We do not offer any support for webmaster related issues and difficulty including coding, &amp; installs, Error solving. if there is an issue where a library or configuration of the server then we can help you if it\'s possible from our end.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Backups - We keep backups but we are not responsible for data loss, you are fully responsible for all backups.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">We Don\'t support any child porn or such material.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">No spam-related sites or material, such as email lists, mass mail programs, and scripts, etc.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">No harassing material that may cause people to retaliate against you.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">No phishing pages.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">You may not run any exploitation script from the server. reason can be terminated immediately.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">If Anyone attempting to hack or exploit the server by using your script or hosting, we will terminate your account to keep safe other users.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Malicious Botnets are strictly forbidden.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Spam, mass mailing, or email marketing in any way are strictly forbidden here.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Malicious hacking materials, trojans, viruses, &amp; malicious bots running or for download are forbidden.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Resource and cronjob abuse is forbidden and will result in suspension or termination.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Php/CGI proxies are strictly forbidden.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">CGI-IRC is strictly forbidden.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">No fake or disposal mailers, mass mailing, mail bombers, SMS bombers, etc.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">NO CREDIT OR REFUND will be granted for interruptions of service, due to User Agreement violations.</li></ul></div>',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => 'refund-policy',
            ],
            [
                'id' => 89,
                'data_keys' => 'policy_pages.element',
                'data_values' => [
                    'title' => 'Working Policy',
                    'details' => '<div class="mb-5" style="margin-bottom:3rem;color:rgb(111,111,111);font-family:Nunito, sans-serif;"><h3 class="mb-3" style="font-weight:600;line-height:1.3;font-size:24px;color:rgb(54,54,54);font-family:Exo, sans-serif;">Terms &amp; Conditions for Users</h3><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">Before getting to this site, you are consenting to be limited by these site Terms and Conditions of Use, every single applicable law, and guidelines, and concur that you are answerable for consistency with any material neighborhood laws. If you disagree with any of these terms, you are restricted from utilizing or getting to this site.</p></div><div class="mb-5" style="margin-bottom:3rem;color:rgb(111,111,111);font-family:Nunito, sans-serif;"><h3 class="mb-3" style="font-weight:600;line-height:1.3;font-size:24px;color:rgb(54,54,54);font-family:Exo, sans-serif;">Support</h3><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">Whenever you have downloaded our item, you may get in touch with us for help through email and we will give a valiant effort to determine your issue. We will attempt to answer using the Email for more modest bug fixes, after which we will refresh the center bundle. Content help is offered to confirmed clients by Tickets as it were. Backing demands made by email and Livechat.</p><p class="my-3 font-18 font-weight-bold" style="font-size:18px;margin-right:0px;margin-left:0px;">On the off chance that your help requires extra adjustment of the System, at that point, you have two alternatives:</p><ul class="font-18" style="padding-left:15px;list-style-type:disc;font-size:18px;"><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Hang tight for additional update discharge.</li><li style="margin-top:0px;margin-right:0px;margin-left:0px;">Or on the other hand, enlist a specialist (We offer customization for extra charges).</li></ul></div><div class="mb-5" style="margin-bottom:3rem;color:rgb(111,111,111);font-family:Nunito, sans-serif;"><h3 class="mb-3" style="font-weight:600;line-height:1.3;font-size:24px;color:rgb(54,54,54);font-family:Exo, sans-serif;">Ownership</h3><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">You may not claim scholarly or selective possession of any of our items, altered or unmodified. All items are property, we created them. Our items are given "with no guarantees" without guarantee of any kind, either communicated or suggested. On no occasion will our juridical individual be subject to any harms including, however not limited to, immediate, roundabout, extraordinary, incidental, or significant harms or different misfortunes emerging out of the utilization of or powerlessness to utilize our items.</p></div><div class="mb-5" style="margin-bottom:3rem;color:rgb(111,111,111);font-family:Nunito, sans-serif;"><h3 class="mb-3" style="font-weight:600;line-height:1.3;font-size:24px;color:rgb(54,54,54);font-family:Exo, sans-serif;">Warranty</h3><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">We don\'t offer any guarantee or assurance of these Services in any way. When our Services have been modified we can\'t ensure they will work with all outsider plugins, modules, or internet browsers. Program similarity ought to be tried against the show formats on the demo worker. If you don\'t mind guarantee that the programs you use will work with the component, as we can not ensure that our systems will work with all program mixes.</p></div><div class="mb-5" style="margin-bottom:3rem;color:rgb(111,111,111);font-family:Nunito, sans-serif;"><h3 class="mb-3" style="font-weight:600;line-height:1.3;font-size:24px;color:rgb(54,54,54);font-family:Exo, sans-serif;">Unauthorized/Illegal Usage</h3><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">You may not utilize our things for any illicit or unapproved reason or may you, in the utilization of the stage, disregard any laws in your locale (counting yet not limited to copyright laws) just as the laws of your nation and International law. In particular, it is prohibited to utilize the things on our foundation for pages that advance: brutality, illegal terrorism, hard sexual entertainment, racism, obscenity content or warez programming links.<br /><br />You can\'t replicate, copy, duplicate, sell, trade or exploit any of our component, utilization of the offered on our things, or admittance to the administration without the express composed consent by us or item proprietor.<br /><br />Our Members are liable for all substance posted on the discussion and demo and movement that happens under your record.<br /><br />We hold the chance of hindering your participation account immediately if we will think about a particularly not allowed conduct.<br /><br />If you make a record on our site, you are liable for keeping up the security of your record, and you are completely answerable for all exercises that happen under the record and some other actions taken regarding the record. You should immediately inform us, of any unapproved employments of your record or some other penetrates of security.</p></div><div class="mb-5" style="margin-bottom:3rem;color:rgb(111,111,111);font-family:Nunito, sans-serif;"><h3 class="mb-3" style="font-weight:600;line-height:1.3;font-size:24px;color:rgb(54,54,54);font-family:Exo, sans-serif;">Fiverr, Seoclerks Sellers Or Affiliates</h3><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">We do NOT guarantee full SEO campaign conveyance within 24 hours. We make no assurance for conveyance time by any means. We give our best assessment to orders during the putting in of requests, anyway, these are gauges. We won\'t be considered liable for loss of assets, negative surveys or you being prohibited for late conveyance. If you are selling on a site that requires time touchy outcomes, utilize Our SEO Services at your own risk.</p></div><div class="mb-5" style="margin-bottom:3rem;color:rgb(111,111,111);font-family:Nunito, sans-serif;"><h3 class="mb-3" style="font-weight:600;line-height:1.3;font-size:24px;color:rgb(54,54,54);font-family:Exo, sans-serif;">Payment/Refund Policy</h3><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">No refund or cash back will be made. After a deposit has been finished, it is extremely unlikely to invert it. You should utilize your equilibrium on requests our administrations, Hosting, SEO campaign. You concur that once you complete a deposit, you won\'t document a debate or a chargeback against us in any way, shape, or form.<br /><br />If you document a debate or chargeback against us after a deposit, we claim all authority to end every single future request, prohibit you from our site. False action, for example, utilizing unapproved or stolen charge cards will prompt the end of your record. There are no special cases.</p></div><div class="mb-5" style="margin-bottom:3rem;color:rgb(111,111,111);font-family:Nunito, sans-serif;"><h3 class="mb-3" style="font-weight:600;line-height:1.3;font-size:24px;color:rgb(54,54,54);font-family:Exo, sans-serif;">Free Balance / Coupon Policy</h3><p class="font-18" style="margin-right:0px;margin-left:0px;font-size:18px;">We offer numerous approaches to get FREE Balance, Coupons and Deposit offers yet we generally reserve the privilege to audit it and deduct it from your record offset with any explanation we may it is a sort of abuse. If we choose to deduct a few or all of free Balance from your record balance, and your record balance becomes negative, at that point the record will naturally be suspended. If your record is suspended because of a negative Balance you can request to make a custom payment to settle your equilibrium to actuate your record.</p></div>',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => 'working-policy',
            ],
            [
                'id' => 90,
                'data_keys' => 'kyc.content',
                'data_values' => [
                    'required' => 'Complete KYC to unlock the full potential of our platform! KYC helps us verify your identity and keep things secure. It is quick and easy just follow the on-screen instructions. Get started with KYC verification now!',
                    'pending' => 'Your KYC verification is being reviewed. We might need some additional information. You will get an email update soon. In the meantime, explore our platform with limited features.',
                    'reject' => 'We regret to inform you that the Know Your Customer (KYC) information provided has been reviewed and unfortunately, it has not met our verification standards.',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => '',
            ],
            [
                'id' => 92,
                'data_keys' => 'register_disable.content',
                'data_values' => [
                    'has_image' => '1',
                    'heading' => 'Registration Currently Disabled',
                    'subheading' => 'Page you are looking for doesn\'t exit or an other error occurred or temporarily unavailable.',
                    'button_name' => 'Go to Home',
                    'button_url' => '#',
                    'image' => '667beeb96fd6c1719398073.png',
                ],
                'seo_content' => null,
                'tempname' => 'basic',
                'slug' => '',
            ],
        ];

        foreach ($FrontEnds as $frontEnd) {
            Frontend::create($frontEnd);
        }
    }
}
