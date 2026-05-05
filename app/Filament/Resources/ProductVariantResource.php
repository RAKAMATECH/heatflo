<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductVariantResource\Pages;
use App\Filament\Resources\ProductVariantResource\RelationManagers;
use App\Models\ProductVariant;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductVariantResource extends Resource
{
    protected static ?string $model = ProductVariant::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?string $navigationLabel = 'Variants';

    protected static ?int $navigationSort = 4;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (! $user instanceof User) {
            return false;
        }

        return $user->isAdmin() || $user->hasRole(User::ROLE_CATALOG) || $user->hasRole(User::ROLE_SALES_FULL);
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        if (! $user instanceof User) {
            return false;
        }

        return $user->isAdmin() || $user->hasRole(User::ROLE_CATALOG);
    }

    public static function canEdit($record): bool
    {
        return static::canCreate();
    }

    public static function canDelete($record): bool
    {
        return static::canCreate();
    }

    public static function form(Form $form): Form
    {
        $attributeKeys = [
            'power_w', 'capacity_kwh', 'voltage_v', 'current_a',
            'efficiency_pct', 'mppt_count', 'max_pv_input_w', 'max_pv_voltage_v',
            'phase', 'frequency_hz', 'warranty_years', 'weight_kg',
            'dimensions_mm', 'colour', 'tank_capacity_l', 'collector_type',
        ];

        return $form
            ->schema([
                Forms\Components\Section::make('Variant details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->placeholder('e.g. 200W, 5kWh, Standard'),
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->placeholder('e.g. HHS-INV-5KW'),
                        Forms\Components\Select::make('currency')
                            ->options(['USD' => 'USD', 'ZWL' => 'ZWL', 'ZAR' => 'ZAR'])
                            ->required()
                            ->default('USD'),
                        Forms\Components\TextInput::make('price_cents')
                            ->label('Price (cents)')
                            ->numeric()
                            ->placeholder('e.g. 129900')
                            ->helperText('In cents: 129900 = $1,299.00. Leave blank if not shown to customers.'),
                        Forms\Components\TextInput::make('stock_qty')
                            ->label('Stock quantity')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->required(),
                    ]),
                Forms\Components\Section::make('Variant image')
                    ->description('Optional image specific to this variant (e.g. different colour or size).')
                    ->schema([
                        Forms\Components\FileUpload::make('image_url')
                            ->label('Upload image')
                            ->disk('public')
                            ->directory('products')
                            ->visibility('public')
                            ->image()
                            ->imagePreviewHeight('200')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(4096)
                            ->openable()
                            ->downloadable()
                            ->helperText('Optional. Overrides the product image for this variant. Max 4 MB.')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Spec attributes')
                    ->description('These appear as product specifications in the storefront.')
                    ->schema([
                        Forms\Components\Repeater::make('attributes')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('key')
                                    ->required()
                                    ->placeholder('e.g. power_w')
                                    ->datalist($attributeKeys),
                                Forms\Components\TextInput::make('value')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g. 200'),
                                Forms\Components\TextInput::make('value_num')
                                    ->label('Numeric value')
                                    ->numeric()
                                    ->placeholder('For sorting/filtering'),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->collapsible()
                            ->itemLabel(fn (array $state) => $state['key'] ?? 'Attribute')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->sortable()
                    ->searchable()
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->copyable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('price_cents')
                    ->label('Price')
                    ->sortable()
                    ->formatStateUsing(function (?int $state, ProductVariant $record): string {
                        if ($state === null || $state === 0) {
                            return '—';
                        }

                        return $record->currency . ' ' . number_format($state / 100, 2);
                    }),
                Tables\Columns\TextColumn::make('stock_qty')
                    ->label('Stock')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state <= 5 => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('product_id')
            ->filters([
                SelectFilter::make('product')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Product'),
                TernaryFilter::make('is_active')
                    ->label('Active')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
                Filter::make('low_stock')
                    ->label('Low / out of stock')
                    ->query(fn (Builder $query) => $query->where('stock_qty', '<=', 5))
                    ->toggle(),
            ])
            ->filtersLayout(\Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AttributesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductVariants::route('/'),
            'create' => Pages\CreateProductVariant::route('/create'),
            'edit' => Pages\EditProductVariant::route('/{record}/edit'),
        ];
    }
}
