<?php

namespace App\Filament\Resources\TipoPersonaResource\Pages;

use App\Filament\Resources\TipoPersonaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTipoPersona extends EditRecord
{
    protected static string $resource = TipoPersonaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
