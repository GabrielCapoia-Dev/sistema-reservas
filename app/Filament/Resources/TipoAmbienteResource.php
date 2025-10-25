<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TipoAmbienteResource\Pages;
use App\Models\TipoAmbiente;
use App\Services\TipoAmbienteService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TipoAmbienteResource extends Resource
{
    protected static ?string $model = TipoAmbiente::class;
    protected static ?string $navigationIcon = 'heroicon-o-paper-clip';
    public static ?string $modelLabel = 'Tipo Ambiente';
    protected static ?string $navigationGroup = "Gerenciamento";
    public static ?string $pluralModelLabel = 'Tipos de Ambientes';
    public static ?string $slug = 'tipo-ambiente';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nome')
                    ->label('Nome:')
                    ->required()
                    ->minLength(3)
                    ->maxLength(50)
                    ->rule('regex:/^\p{L}+(?:\s\p{L}+)*$/u')
                    ->validationMessages([
                        'regex' => 'Use apenas letras, sem caracteres especiais.',
                    ])
                    ->unique(ignoreRecord: true),

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
        return app(TipoAmbienteService::class)->configurarTabela($table, Auth::user());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTipoAmbientes::route('/'),
        ];
    }
}
