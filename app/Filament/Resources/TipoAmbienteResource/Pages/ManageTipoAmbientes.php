<?php

namespace App\Filament\Resources\TipoAmbienteResource\Pages;

use App\Filament\Resources\TipoAmbienteResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTipoAmbientes extends ManageRecords
{
    protected static string $resource = TipoAmbienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
