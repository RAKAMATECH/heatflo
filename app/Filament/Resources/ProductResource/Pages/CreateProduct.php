<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

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
