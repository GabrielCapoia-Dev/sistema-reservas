<?php

namespace App\Services;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;

class AmbienteService
{
    //** Retorna a configuracao da tabela */
    public function configurarTabela(Table $table, ?User $user): Table
    {
        return $table
            ->paginated([10, 25, 50, 100])
            ->columns($this->colunasTabela())
            ->actions($this->acoesTabela($user))
            ->bulkActions($this->acoesEmMassa($user))
            ->defaultSort('updated_at', 'desc')
            ->striped();
    }

    //** Retorna as acoes em massa da tabela */
    private function acoesEmMassa(?User $user): array
    {
        return [
            Tables\Actions\DeleteBulkAction::make()
        ];
    }

    //** Retorna as acoes da tabela */
    private function acoesTabela(?User $user): array
    {
        return [
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make()
        ];
    }

    //** Retorna as colunas da tabela */
    private function colunasTabela(): array
    {
        return [
            Tables\Columns\TextColumn::make('nome')
                ->label('Nome')
                ->wrap()
                ->sortable()
                ->searchable(),

            Tables\Columns\ToggleColumn::make('status')
                ->label('Status')
                ->sortable()
                ->inline(false)
                ->onColor('success')
                ->offColor('danger')
                ->onIcon('heroicon-s-check')
                ->offIcon('heroicon-s-x-mark')
                ->columnSpan(1),

            Tables\Columns\TextColumn::make('capacidade')
                ->label('Capacidade')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('tipoAmbiente.nome')
                ->label('Tipo Ambiente')
                ->sortable()
                ->badge()
                ->searchable(),
        ];
    }
}
