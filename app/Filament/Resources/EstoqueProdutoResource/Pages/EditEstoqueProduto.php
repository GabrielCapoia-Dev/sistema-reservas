<?php

namespace App\Filament\Resources\EstoqueProdutoResource\Pages;

use App\Filament\Resources\EstoqueProdutoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEstoqueProduto extends EditRecord
{
    protected static string $resource = EstoqueProdutoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
