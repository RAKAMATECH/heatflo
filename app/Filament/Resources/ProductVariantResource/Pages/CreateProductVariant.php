<?php

namespace App\Filament\Resources\ProductVariantResource\Pages;

use App\Filament\Resources\ProductVariantResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProductVariant extends CreateRecord
{
    protected static string $resource = ProductVariantResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (
            isset($data['image_url']) &&
            $data['image_url'] !== '' &&
            ! str_starts_with((string) $data['image_url'], 'products/')
        ) {
            $data['image_url'] = 'products/' . $data['image_url'];
        }

        return $data;
    }
}
