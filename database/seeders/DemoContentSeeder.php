<?php

namespace Database\Seeders;

use App\Models\ContentPage;
use App\Models\ContentPost;
use Illuminate\Database\Seeder;

class DemoContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ContentPage::updateOrCreate(
            ['pathname' => '/'],
            [
                'slug' => 'home',
                'title' => 'Heatflo Home Solutions',
                'excerpt' => 'Reliable Solar, Heating & Electrical Solutions in Zimbabwe',
                'body_html' => '<p>Welcome to Heatflo Home Solutions.</p>',
                'blocks' => null,
                'is_published' => true,
                'published_at' => now(),
            ]
        );

        $servicePages = [
            ['/solar-solutions/', 'solar-solutions', 'Solar Solutions'],
            ['/solar-solutions/inverters/', 'solar-inverters', 'Inverters'],
            ['/solar-solutions/batteries/', 'solar-batteries', 'Batteries'],
            ['/solar-solutions/solar-panels/', 'solar-panels', 'Solar Panels'],
            ['/solar-solutions/solar-accessories/', 'solar-accessories', 'Solar Accessories'],
            ['/fireplaces-heating/', 'fireplaces-heating', 'Heating Solutions'],
            ['/fireplaces-heating/fireplaces/', 'fireplaces', 'Fireplaces'],
            ['/fireplaces-heating/flues/', 'flues', 'Flues'],
            ['/electrical-services/', 'electrical-services', 'Electrical Services'],
            ['/quote', 'quote', 'Request a Quote'],
            ['/contact', 'contact', 'Contact'],
            ['/resources', 'resources', 'Resources'],
        ];

        foreach ($servicePages as [$pathname, $slug, $title]) {
            ContentPage::updateOrCreate(
                ['pathname' => $pathname],
                [
                    'slug' => $slug,
                    'title' => $title,
                    'excerpt' => null,
                    'body_html' => '<p>Content coming soon.</p>',
                    'blocks' => null,
                    'is_published' => true,
                    'published_at' => now(),
                ]
            );
        }

        ContentPost::updateOrCreate(
            ['slug' => 'welcome'],
            [
                'title' => 'Welcome',
                'excerpt' => 'Welcome to the Heatflo resources library.',
                'body_html' => '<p>This is a demo resource post.</p>',
                'blocks' => null,
                'is_published' => true,
                'published_at' => now(),
            ]
        );
    }
}
