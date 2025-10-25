<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AmbienteResource\Pages;
use App\Filament\Resources\AmbienteResource\RelationManagers;
use App\Models\Ambiente;
use App\Services\AmbienteService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class AmbienteResource extends Resource
{
    protected static ?string $model = Ambiente::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    public static ?string $modelLabel = 'Ambiente';
    protected static ?string $navigationGroup = "Gerenciamento";
    public static ?string $pluralModelLabel = 'Ambientes';
    public static ?string $slug = 'ambiente';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nome')
                    ->label('Nome:')
                    ->required()
                    ->minLength(3)
                    ->maxLength(50)
                    ->validationMessages([
                        'regex' => 'Use apenas letras, sem caracteres especiais.',
                    ])
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('capacidade')
                    ->label('Capacidade:')
                    ->helperText('Quantidade lugares neste ambiente.')
                    ->required()
                    ->numeric(),

                Forms\Components\Select::make('tipo_ambiente_id')
                    ->label('Tipo Ambiente')
                    ->relationship('tipoAmbiente', 'nome')
                    ->required()
                    ->preload(),

                Forms\Components\Toggle::make('status')
                    ->label('Status')
                    ->inline(false)
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-s-check')
                    ->offIcon('heroicon-s-x-mark')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return app(AmbienteService::class)->configurarTabela($table, Auth::user());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAmbientes::route('/'),
        ];
    }
}
