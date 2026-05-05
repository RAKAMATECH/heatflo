<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentLeadsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

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

    public function table(Table $table): Table
    {
        return $table
            ->query(Lead::query()->latest())
            ->defaultPaginationPageOption(10)
            ->columns([
                TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('email')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->searchable()
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('service')
                    ->searchable()
                    ->badge(),
                TextColumn::make('location')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('subject')
                    ->searchable()
                    ->limit(40),
            ]);
    }
}
