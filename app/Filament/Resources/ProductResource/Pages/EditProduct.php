<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (isset($data['image_url']) && str_starts_with((string) $data['image_url'], 'products/')) {
            $data['image_url'] = substr($data['image_url'], strlen('products/'));
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
