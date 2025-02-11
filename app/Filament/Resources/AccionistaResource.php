<?php
namespace App\Filament\Resources;

use App\Filament\Resources\AccionistaResource\Pages;
use App\Models\Accionista;
use App\Models\Empresa;
use App\Models\TipoPersona;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class AccionistaResource extends Resource
{
    protected static ?string $model = Accionista::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Select::make('tipo_persona_id')
                    ->label('Tipo de Persona')
                    ->options(TipoPersona::pluck('nombre', 'id'))
                    ->required(),

                TextInput::make('numero_identificacion')
                    ->label('Número de Identificación')
                    ->maxLength(30),

                TextInput::make('nombre')
                    ->label('Nombre')
                    ->required(),

                TextInput::make('participacion_accionaria')
                    ->label('Participación Accionaria')
                    ->numeric()
                    ->required(),

                Select::make('id_padre')
                    ->label('Accionista Padre')
                    ->options(Accionista::pluck('nombre', 'id'))
                    ->searchable()
                    ->nullable(),

                Select::make('empresa_id')
                    ->label('Empresa')
                    ->options(Empresa::pluck('razon_social', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('estado')
                    ->label('Estado')
                    ->options([
                        1 => 'Activo',
                        0 => 'Inactivo',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->label('Nombre'),
                TextColumn::make('tipoPersona.nombre')->label('Tipo de Persona'),
                TextColumn::make('numero_identificacion')->label('Identificación'),
                TextColumn::make('participacion_accionaria')->label('Participación %'),
                TextColumn::make('empresa.nombre')->label('Empresa'),
                TextColumn::make('accionistaPadre.nombre')->label('Accionista Padre')->sortable(),
                TextColumn::make('estado')->label('Estado')->badge()
                    ->formatStateUsing(fn($state) => $state ? 'Activo' : 'Inactivo')
                    ->colors([
                        'success' => 1,
                        'danger' => 0,
                    ]),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccionistas::route('/'),
            'create' => Pages\CreateAccionista::route('/create'),
            'edit' => Pages\EditAccionista::route('/{record}/edit'),
        ];
    }
}
