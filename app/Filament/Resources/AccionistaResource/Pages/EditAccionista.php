<?php

namespace App\Filament\Resources\AccionistaResource\Pages;

use App\Filament\Resources\AccionistaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAccionista extends EditRecord
{
    protected static string $resource = AccionistaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
