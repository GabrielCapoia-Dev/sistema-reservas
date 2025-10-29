<?php

// app/Livewire/VerEstoqueTable.php
namespace App\Livewire;

use App\Models\Estoque;
use App\Models\EstoqueProduto;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Livewire\Component;

class VerEstoqueTable extends Component
{
    use InteractsWithTable;

    public ?Estoque $estoque = null;

    public function mount(Estoque $estoque): void
    {
        $this->estoque = $estoque;
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                EstoqueProduto::query()
                    ->with(['produto.categoria', 'estoque'])
                    ->where('estoque_id', $this->estoque->id)
            )
            ->columns([
                Tables\Columns\TextColumn::make('estoque.nome')
                    ->label('Estoque')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('produto.nome')
                    ->label('Produto')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('produto.categoria.nome')
                    ->label('Categoria')
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantidade')
                    ->label('Quantidade')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('produto.preco')
                    ->label('Preço')
                    ->money('BRL', locale: 'pt_BR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->state(fn (EstoqueProduto $record) => ($record->quantidade ?? 0) * (float) ($record->produto->preco ?? 0))
                    ->money('BRL', locale: 'pt_BR')
                    ->sortable(),
            ])
            ->defaultSort('produto.nome')
            ->paginated([10, 25, 50])
            ->emptyStateHeading('Nenhum produto vinculado a este estoque.')
            ->filters([])   // adicione filtros se quiser
            ->actions([])   // tabela só de leitura no modal
            ->bulkActions([]);
    }

    public function render()
    {
        return view('components.ver-estoque');
    }
}

