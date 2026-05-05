<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $main = Menu::updateOrCreate(
            ['key' => 'main'],
            ['name' => 'Main Navigation'],
        );

        $footer = Menu::updateOrCreate(
            ['key' => 'footer'],
            ['name' => 'Footer Links'],
        );

        MenuItem::query()->where('menu_id', $main->id)->delete();
        MenuItem::query()->where('menu_id', $footer->id)->delete();

        $solar = MenuItem::create([
            'menu_id' => $main->id,
            'label' => 'Solar Solutions',
            'page_pathname' => '/solar-solutions/',
            'sort_order' => 20,
            'is_active' => true,
        ]);

        $heating = MenuItem::create([
            'menu_id' => $main->id,
            'label' => 'Heating Solutions',
            'page_pathname' => '/fireplaces-heating/',
            'sort_order' => 30,
            'is_active' => true,
        ]);

        MenuItem::create([
            'menu_id' => $main->id,
            'label' => 'Home',
            'page_pathname' => '/',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        MenuItem::create([
            'menu_id' => $main->id,
            'parent_id' => $solar->id,
            'label' => 'Inverters',
            'page_pathname' => '/solar-solutions/inverters/',
            'sort_order' => 10,
            'is_active' => true,
        ]);
        MenuItem::create([
            'menu_id' => $main->id,
            'parent_id' => $solar->id,
            'label' => 'Batteries',
            'page_pathname' => '/solar-solutions/batteries/',
            'sort_order' => 20,
            'is_active' => true,
        ]);
        MenuItem::create([
            'menu_id' => $main->id,
            'parent_id' => $solar->id,
            'label' => 'Solar Panels',
            'page_pathname' => '/solar-solutions/solar-panels/',
            'sort_order' => 30,
            'is_active' => true,
        ]);
        MenuItem::create([
            'menu_id' => $main->id,
            'parent_id' => $solar->id,
            'label' => 'Solar Accessories',
            'page_pathname' => '/solar-solutions/solar-accessories/',
            'sort_order' => 40,
            'is_active' => true,
        ]);

        MenuItem::create([
            'menu_id' => $main->id,
            'parent_id' => $heating->id,
            'label' => 'Fireplaces',
            'page_pathname' => '/fireplaces-heating/fireplaces/',
            'sort_order' => 10,
            'is_active' => true,
        ]);
        MenuItem::create([
            'menu_id' => $main->id,
            'parent_id' => $heating->id,
            'label' => 'Flues',
            'page_pathname' => '/fireplaces-heating/flues/',
            'sort_order' => 20,
            'is_active' => true,
        ]);

        MenuItem::create([
            'menu_id' => $main->id,
            'label' => 'Electrical Services',
            'page_pathname' => '/electrical-services/',
            'sort_order' => 40,
            'is_active' => true,
        ]);
        MenuItem::create([
            'menu_id' => $main->id,
            'label' => 'Catalog',
            'page_pathname' => '/catalog',
            'sort_order' => 50,
            'is_active' => true,
        ]);
        MenuItem::create([
            'menu_id' => $main->id,
            'label' => 'Resources',
            'page_pathname' => '/resources',
            'sort_order' => 60,
            'is_active' => true,
        ]);
        MenuItem::create([
            'menu_id' => $main->id,
            'label' => 'Account',
            'page_pathname' => '/account',
            'sort_order' => 70,
            'is_active' => true,
        ]);
        MenuItem::create([
            'menu_id' => $main->id,
            'label' => 'Request a Quote',
            'page_pathname' => '/contact',
            'sort_order' => 80,
            'is_active' => true,
        ]);

        MenuItem::create([
            'menu_id' => $footer->id,
            'label' => 'Resources',
            'page_pathname' => '/resources',
            'sort_order' => 10,
            'is_active' => true,
        ]);
        MenuItem::create([
            'menu_id' => $footer->id,
            'label' => 'Account',
            'page_pathname' => '/account',
            'sort_order' => 20,
            'is_active' => true,
        ]);
        MenuItem::create([
            'menu_id' => $footer->id,
            'label' => 'Request a Quote',
            'page_pathname' => '/quote',
            'sort_order' => 30,
            'is_active' => true,
        ]);
    }
}
