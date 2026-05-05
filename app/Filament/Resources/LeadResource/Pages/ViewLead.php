<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewLead extends ViewRecord
{
    protected static string $resource = LeadResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Lead')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Received')
                            ->dateTime(),
                        TextEntry::make('source')
                            ->label('Source')
                            ->placeholder('—'),
                        TextEntry::make('service')
                            ->label('Service')
                            ->placeholder('—'),
                        TextEntry::make('location')
                            ->label('Location')
                            ->placeholder('—'),
                        TextEntry::make('name')
                            ->label('Name'),
                        TextEntry::make('email')
                            ->label('Email')
                            ->placeholder('—'),
                        TextEntry::make('phone')
                            ->label('Phone')
                            ->placeholder('—'),
                        TextEntry::make('subject')
                            ->label('Subject')
                            ->placeholder('—')
                            ->columnSpanFull(),
                        TextEntry::make('message')
                            ->label('Message')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
