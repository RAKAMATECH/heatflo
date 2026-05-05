<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            AdminUserSeeder::class,
            MenuSeeder::class,
        ]);

        if (filter_var(env('DEMO_CATALOG_SEED', false), FILTER_VALIDATE_BOOL)) {
            $this->call([
                DemoCatalogSeeder::class,
            ]);
        }

        if (filter_var(env('DEMO_CONTENT_SEED', false), FILTER_VALIDATE_BOOL)) {
            $this->call([
                DemoContentSeeder::class,
            ]);
        }
    }
}
