<?php

namespace App\Filament\Resources\AccionistaResource\Pages;

use App\Filament\Resources\AccionistaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAccionistas extends ListRecords
{
    protected static string $resource = AccionistaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
