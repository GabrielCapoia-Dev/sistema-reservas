<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstoqueProdutoResource\Pages;
use App\Models\Estoque;
use App\Models\EstoqueProduto;
use App\Models\Produto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class EstoqueProdutoResource extends Resource
{
    protected static ?string $model = EstoqueProduto::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Produtos por Estoque';
    protected static ?string $pluralLabel = 'Produtos por Estoque';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('estoque_id')
                ->label('Estoque')
                ->options(fn () => Estoque::query()->orderBy('nome')->pluck('nome', 'id'))
                ->searchable()
                ->preload()
                ->required(),

            Select::make('produto_id')
                ->label('Produto')
                ->options(fn () => Produto::query()->orderBy('nome')->pluck('nome', 'id'))
                ->searchable()
                ->preload()
                ->required()
                // evita duplicidade produto x estoque
                ->rule(function (callable $get) {
                    $estoqueId = $get('estoque_id');
                    return $estoqueId
                        ? Rule::unique('estoque_produto', 'produto_id')->where('estoque_id', $estoqueId)
                        : null;
                })
                ->validationAttribute('produto'),

            TextInput::make('quantidade')
                ->label('Quantidade')
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('estoque.nome')->label('Estoque')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('produto.nome')->label('Produto')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('quantidade')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEstoqueProdutos::route('/'),
            'create' => Pages\CreateEstoqueProduto::route('/create'),
            'edit' => Pages\EditEstoqueProduto::route('/{record}/edit'),
        ];
    }
}
