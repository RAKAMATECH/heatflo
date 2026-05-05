<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = (string) env('ADMIN_EMAIL', 'admin@heatflo.co.zw');
        $name = (string) env('ADMIN_NAME', 'Heatflo Admin');
        $password = (string) env('ADMIN_PASSWORD', 'password');

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'is_admin' => true,
                'role' => User::ROLE_ADMIN,
            ],
        );

        if (!$user->wasRecentlyCreated && !$user->is_admin) {
            $user->forceFill(['is_admin' => true])->save();
        }

        if (!$user->wasRecentlyCreated && $user->role !== User::ROLE_ADMIN) {
            $user->forceFill(['role' => User::ROLE_ADMIN])->save();
        }
    }
}
