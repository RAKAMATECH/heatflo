<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?string $navigationLabel = 'Products';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (! $user instanceof User) {
            return false;
        }

        return $user->isAdmin() || $user->hasRole(User::ROLE_CATALOG) || $user->hasAnyRole([
            User::ROLE_SALES_FULL,
            User::ROLE_SALES_PRODUCTS_LEADS,
        ]);
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
        $slugAutoFill = fn (?string $state, callable $set, $get) => (
            ! $get('slug') && $state ? $set('slug', Str::slug($state)) : null
        );

        $categoryCreateForm = [
            Forms\Components\TextInput::make('name')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn (?string $state, callable $set, $get) => (
                    ! $get('slug') && $state ? $set('slug', Str::slug($state)) : null
                )),
            Forms\Components\TextInput::make('slug')->required()->maxLength(255),
        ];

        $brandCreateForm = [
            Forms\Components\TextInput::make('name')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn (?string $state, callable $set, $get) => (
                    ! $get('slug') && $state ? $set('slug', Str::slug($state)) : null
                )),
            Forms\Components\TextInput::make('slug')->required()->maxLength(255),
        ];

        $attributeKeys = [
            'power_w', 'capacity_kwh', 'voltage_v', 'current_a',
            'efficiency_pct', 'mppt_count', 'max_pv_input_w', 'max_pv_voltage_v',
            'phase', 'frequency_hz', 'warranty_years', 'weight_kg',
            'dimensions_mm', 'colour', 'tank_capacity_l', 'collector_type',
        ];

        return $form
            ->schema([
                Forms\Components\Tabs::make('Product')
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Info')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Section::make('Identification')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated($slugAutoFill)
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('slug')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true)
                                            ->helperText('Auto-filled from name. Used in the storefront URL.')
                                            ->columnSpanFull(),
                                        Forms\Components\Select::make('category_id')
                                            ->label('Category')
                                            ->relationship('category', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm($categoryCreateForm)
                                            ->required(),
                                        Forms\Components\Select::make('brand_id')
                                            ->label('Brand')
                                            ->relationship('brand', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm($brandCreateForm),
                                    ]),
                                Forms\Components\Section::make('Visibility')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Active')
                                            ->helperText('Inactive products are hidden from the storefront.')
                                            ->default(true)
                                            ->required(),
                                        Forms\Components\Toggle::make('is_featured')
                                            ->label('Featured on homepage')
                                            ->helperText('Appears in the featured section on the home page.')
                                            ->required(),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Content')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Section::make('Descriptions')
                                    ->schema([
                                        Forms\Components\Textarea::make('short_description')
                                            ->label('Short description')
                                            ->helperText('One or two sentences shown on product cards.')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('description')
                                            ->label('Full description / overview')
                                            ->helperText('Rendered as HTML on the product page.')
                                            ->columnSpanFull(),
                                    ]),
                                Forms\Components\Section::make('Product image')
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
                                            ->helperText('Recommended: 800×600px or larger, JPG/PNG/WebP. Max 4 MB.')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Variants & Specs')
                            ->icon('heroicon-o-adjustments-horizontal')
                            ->schema([
                                Forms\Components\Repeater::make('variants')
                                    ->relationship()
                                    ->label('Variants')
                                    ->helperText('Each variant represents a SKU with its own price, stock, and spec attributes.')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('title')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->default('Standard')
                                                    ->placeholder('e.g. 200W, 5kWh, Standard'),
                                                Forms\Components\TextInput::make('sku')
                                                    ->label('SKU')
                                                    ->maxLength(255)
                                                    ->placeholder('e.g. HHS-INV-5KW'),
                                                Forms\Components\Select::make('currency')
                                                    ->options(['USD' => 'USD', 'ZWL' => 'ZWL', 'ZAR' => 'ZAR'])
                                                    ->required()
                                                    ->default('USD'),
                                            ]),
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('price_cents')
                                                    ->label('Price (cents)')
                                                    ->numeric()
                                                    ->placeholder('e.g. 129900')
                                                    ->helperText('In cents: 129900 = $1,299.00. Leave blank if not displayed.'),
                                                Forms\Components\TextInput::make('stock_qty')
                                                    ->label('Stock qty')
                                                    ->required()
                                                    ->numeric()
                                                    ->default(0),
                                                Forms\Components\Toggle::make('is_active')
                                                    ->label('Active')
                                                    ->default(true)
                                                    ->required(),
                                            ]),
                                        Forms\Components\Repeater::make('attributes')
                                            ->relationship()
                                            ->label('Spec attributes')
                                            ->helperText('These appear as product specs in the storefront.')
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
                                            ->collapsed()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state) => $state['key'] ?? 'Attribute'),
                                    ])
                                    ->defaultItems(1)
                                    ->collapsed()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state) => $state['title'] ?? 'Variant')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('')
                    ->disk('public')
                    ->width(48)
                    ->height(48)
                    ->defaultImageUrl(asset('product-placeholder.svg'))
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover']),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->description(fn (Product $record) => $record->short_description
                        ? \Illuminate\Support\Str::limit($record->short_description, 60)
                        : null),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('brand.name')
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('variants_count')
                    ->label('Variants')
                    ->counts('variants')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Category'),
                SelectFilter::make('brand')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Brand'),
                TernaryFilter::make('is_active')
                    ->label('Active')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
                TernaryFilter::make('is_featured')
                    ->label('Featured')
                    ->trueLabel('Featured only')
                    ->falseLabel('Not featured'),
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
                    Tables\Actions\BulkAction::make('feature')
                        ->label('Mark as featured')
                        ->icon('heroicon-o-star')
                        ->color('info')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_featured' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\VariantsRelationManager::class,
            RelationManagers\CompatibilitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
