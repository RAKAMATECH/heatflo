<?php

namespace App\Filament\Widgets;

use App\Models\ProductVariant;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockVariantsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();
        if (! $user instanceof User) {
            return false;
        }

        return $user->isAdmin() || $user->hasRole(User::ROLE_CATALOG) || $user->hasRole(User::ROLE_SALES_FULL);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProductVariant::query()
                    ->with(['product', 'product.brand', 'product.category'])
                    ->orderBy('stock_qty', 'asc')
                    ->orderBy('updated_at', 'desc')
            )
            ->defaultPaginationPageOption(10)
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('title')
                    ->label('Variant')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('stock_qty')
                    ->label('Stock')
                    ->sortable()
                    ->badge(),
                TextColumn::make('product.category.name')
                    ->label('Category')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('product.brand.name')
                    ->label('Brand')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->since()
                    ->sortable(),
            ])
            ->recordUrl(function (ProductVariant $record): ?string {
                $user = auth()->user();
                if (! $user instanceof User) {
                    return null;
                }

                if (! ($user->isAdmin() || $user->hasRole(User::ROLE_CATALOG) || $user->hasRole(User::ROLE_SALES_FULL))) {
                    return null;
                }

                return route('filament.admin.resources.product-variants.edit', ['record' => $record]);
            });
    }
}
