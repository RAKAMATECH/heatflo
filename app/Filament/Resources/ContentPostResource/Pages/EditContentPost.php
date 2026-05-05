<?php

namespace App\Filament\Resources\ContentPostResource\Pages;

use App\Filament\Resources\ContentPostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContentPost extends EditRecord
{
    protected static string $resource = ContentPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
