<?php

namespace App\Filament\Widgets;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Lead;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminKpiStatsWidget extends BaseWidget
{
    public static function canView(): bool
    {
        $user = auth()->user();
        if (! $user instanceof User) {
            return false;
        }

        return $user->isAdmin() || $user->hasRole(User::ROLE_CATALOG) || $user->hasAnyRole([
            User::ROLE_SALES_FULL,
            User::ROLE_SALES_LEADS_ONLY,
            User::ROLE_SALES_PRODUCTS_LEADS,
        ]);
    }

    protected function getStats(): array
    {
        $productsTotal = Product::query()->count();
        $productsActive = Product::query()->where('is_active', true)->count();
        $productsFeatured = Product::query()->where('is_featured', true)->count();

        $categories = Category::query()->count();
        $brands = Brand::query()->count();

        $leads7d = Lead::query()->where('created_at', '>=', now()->subDays(7))->count();

        return [
            Stat::make('Products', $productsTotal)
                ->description($productsActive.' active')
                ->color('primary'),

            Stat::make('Featured', $productsFeatured)
                ->description('Homepage')
                ->color('warning'),

            Stat::make('Categories', $categories)
                ->description($brands.' brands')
                ->color('gray'),

            Stat::make('Leads (7d)', $leads7d)
                ->description('New enquiries')
                ->color('success'),
        ];
    }
}
