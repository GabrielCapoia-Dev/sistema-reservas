<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DominioEmailResource\Pages;
use App\Filament\Resources\DominioEmailResource\RelationManagers;
use App\Models\DominioEmail;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class DominioEmailResource extends Resource
{
    protected static ?string $model = DominioEmail::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = "Acesso";

    public static ?string $label = 'Dominio Permitido';

    public static ?string $pluralLabel = 'Dominios Permitidos';

    public static function getNavigationBadge(): ?string
    {
        $value = (string) static::getModel()::count();

        if ($value > 0) {
            return $value;
        }

        return null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Quantidade de dominios permitidos';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('dominio_email')
                    ->label('Dominio Permitido')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->rule('regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/')
                    ->helperText('Digite o dominio sem o @, exemplo: dominio.com.br')
                    ->placeholder('dominio.com.br'),

                TextInput::make('nome')
                    ->label('Nome:')
                    ->required()
                    ->minLength(3)
                    ->maxLength(100)
                    ->rule('regex:/^\p{L}+(?:\s\p{L}+)*$/u')
                    ->validationMessages([
                        'regex' => 'Use apenas letras, sem caracteres especiais.',
                    ]),

                Toggle::make('status')
                    ->label('Status')
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-s-check')
                    ->offIcon('heroicon-s-x-mark')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100])
            ->columns([
                TextColumn::make('dominio_email')
                    ->label('Email Dominio')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('setor')
                    ->label('Setor')
                    ->searchable()
                    ->sortable(),
                ToggleColumn::make('status')
                    ->label('Status')
                    ->inline(false)
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-s-check')
                    ->offIcon('heroicon-s-x-mark')
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
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(function () {
                            /** @var \App\Models\User|null $user */
                            $user = Auth::user();
                            if (!$user) {
                                return false;
                            }
                            return $user->hasRole('Admin');
                        }),
                ]),

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDominioEmails::route('/'),
        ];
    }
}
