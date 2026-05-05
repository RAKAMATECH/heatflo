<?php

namespace App\Filament\Resources\ProductVariantAttributeResource\Pages;

use App\Filament\Resources\ProductVariantAttributeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductVariantAttribute extends EditRecord
{
    protected static string $resource = ProductVariantAttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
