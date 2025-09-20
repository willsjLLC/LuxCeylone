<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'id' => 1,
                'name' => 'HOME',
                'slug' => '/',
                'tempname' => 'templates.basic.',
                'secs' => '["counter","overview","top_freelancer","job_post","job_category","blog"]',
                'seo_content' => null,
                'is_default' => 1,
            ],
            [
                'id' => 4,
                'name' => 'Blog',
                'slug' => 'blog',
                'tempname' => 'templates.basic.',
                'secs' => null,
                'seo_content' => null,
                'is_default' => 1,
            ],
            [
                'id' => 5,
                'name' => 'Contact',
                'slug' => 'contact',
                'tempname' => 'templates.basic.',
                'secs' => null,
                'seo_content' => null,
                'is_default' => 1,
            ],
            [
                'id' => 21,
                'name' => 'FAQs',
                'slug' => 'faq',
                'tempname' => 'templates.basic.',
                'secs' => '["faq"]',
                'seo_content' => null,
                'is_default' => 0
            ],
        ];

        foreach ($pages as $page) {
            Page::create($page);
        }
    }
}
