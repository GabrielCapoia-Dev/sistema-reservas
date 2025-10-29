<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstoqueResource\Pages;
use App\Models\Estoque;
use App\Models\Produto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\DB;

class EstoqueResource extends Resource
{
    protected static ?string $model = Estoque::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nome')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('descricao')
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('descricao')->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->label('Criado em')->since(),
                Tables\Columns\TextColumn::make('updated_at')->label('Atualizado em')->since(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\Action::make('adicionarProduto')
                    ->label('Adicionar produto(s)')
                    ->icon('heroicon-o-plus')
                    ->modalHeading(fn(Estoque $record) => "Adicionar produto(s) ao estoque: {$record->nome}")
                    ->form([
                        Section::make()
                            ->schema([
                                Repeater::make('itens')
                                    ->label('Itens')
                                    ->minItems(1)
                                    ->defaultItems(1)
                                    ->columns(12)
                                    ->schema([
                                        Select::make('produto_id')
                                            ->label('Produto')
                                            ->options(fn() => \App\Models\Produto::query()
                                                ->orderBy('nome')
                                                ->pluck('nome', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->columnSpan(7),

                                        TextInput::make('quantidade')
                                            ->label('Quantidade')
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(1)
                                            ->required()
                                            ->columnSpan(3),

                                        Toggle::make('incrementar')
                                            ->label('Somar?')
                                            ->helperText('Somar à quantidade existente (em vez de sobrescrever).')
                                            ->default(true)
                                            ->columnSpan(2),
                                    ])
                                    ->addActionLabel('Adicionar outro produto'),
                            ]),
                    ])
                    ->action(function (Estoque $record, array $data) {
                        $itens = $data['itens'] ?? [];

                        if (empty($itens)) {
                            \Filament\Notifications\Notification::make()
                                ->title('Nenhum item informado.')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Opcional: consolidar linhas repetidas do mesmo produto (somando quantidades quando "incrementar" = true)
                        // Isso evita bater duas vezes no mesmo produto_id dentro do mesmo submit
                        $consolidados = [];
                        foreach ($itens as $item) {
                            $pid = (int) ($item['produto_id'] ?? 0);
                            $qtd = max(0, (int) ($item['quantidade'] ?? 0));
                            $inc = (bool) ($item['incrementar'] ?? true);
                            if ($pid <= 0) {
                                continue;
                            }
                            // Agrupa por produto; se quaisquer linhas marcarem "incrementar",
                            // somamos as quantidades dessa submissão para esse produto.
                            if (! isset($consolidados[$pid])) {
                                $consolidados[$pid] = ['quantidade' => 0, 'incrementar' => $inc];
                            }
                            if ($inc) {
                                $consolidados[$pid]['quantidade'] += $qtd;
                                $consolidados[$pid]['incrementar'] = true;
                            } else {
                                // sobrescrever: mantemos a última quantidade explicitada
                                $consolidados[$pid]['quantidade'] = $qtd;
                                // se já havia "incrementar=true" acumulado antes, manter sobrescrita como prioridade?
                                // Aqui priorizamos "sobrescrever" se aparecer ao menos uma linha com incrementar=false
                                $consolidados[$pid]['incrementar'] = $consolidados[$pid]['incrementar'] && false;
                            }
                        }

                        DB::transaction(function () use ($record, $consolidados) {
                            foreach ($consolidados as $produtoId => $payload) {
                                $qtd = (int) $payload['quantidade'];
                                $inc = (bool) $payload['incrementar'];

                                $pivotRow = $record->produtos()->where('produto_id', $produtoId)->first();

                                if ($pivotRow && $pivotRow->pivot) {
                                    $atual = (int) $pivotRow->pivot->quantidade;
                                    $novaQuantidade = $inc ? ($atual + $qtd) : $qtd;

                                    $record->produtos()
                                        ->updateExistingPivot($produtoId, ['quantidade' => $novaQuantidade]);
                                } else {
                                    $record->produtos()
                                        ->attach($produtoId, ['quantidade' => $qtd]);
                                }
                            }
                        });

                        \Filament\Notifications\Notification::make()
                            ->title('Produtos registrados no estoque com sucesso!')
                            ->success()
                            ->send();
                    })
                    ->modalSubmitActionLabel('Salvar')
                    ->closeModalByClickingAway(false),

                Actions\Action::make('verEstoque')
                    ->label('Ver estoque')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn(Estoque $record) => "Estoque: {$record->nome}")
                    ->modalWidth('7xl')
                    ->modalSubmitAction(false)   // só leitura; sem botão "Salvar"
                    ->modalContent(function (Estoque $record) {
                        // Carrega produtos + categoria e monta linhas da tabela
                        $produtos = $record->produtos()
                            ->with('categoria')
                            ->orderBy('nome')
                            ->get();

                        // Mapeia para estrutura simples de exibição
                        $linhas = $produtos->map(function ($p) use ($record) {
                            $qtd = (int) ($p->pivot->quantidade ?? 0);
                            $preco = (float) ($p->preco ?? 0);
                            return [
                                'estoque'   => $record->nome,
                                'produto'   => $p->nome,
                                'categoria' => $p->categoria->nome ?? '—',
                                'quantidade' => $qtd,
                                'preco'     => $preco,
                                'total'     => $qtd * $preco,
                            ];
                        });

                        $totalGeral = $linhas->sum('total');

                        return view('components.ver-estoque', [
                            'linhas'     => $linhas,
                            'totalGeral' => $totalGeral,
                        ]);
                    }),


                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageEstoques::route('/'),
        ];
    }
}
