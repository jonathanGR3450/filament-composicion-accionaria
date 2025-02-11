<?php
namespace App\Filament\Resources;

use App\Filament\Resources\AccionistaResource\Pages;
use App\Models\Accionista;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Mail;

class AccionistaResource extends Resource
{
    protected static ?string $model = Accionista::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Gestión Empresarial';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Select::make('tipo_persona_id')
                    ->label('Tipo de Persona')
                    ->relationship('tipoPersona', 'nombre')
                    ->required(),

                TextInput::make('numero_identificacion')
                    ->label('Número de Identificación')
                    ->required(),

                TextInput::make('nombre')
                    ->label('Nombre o Razón Social')
                    ->required(),

                TextInput::make('participacion_accionaria')
                    ->label('Participación Accionaria (%)')
                    ->numeric()
                    ->required(),

                Select::make('id_padre')
                    ->label('Accionista Padre')
                    ->relationship('accionistaPadre', 'nombre')
                    ->nullable(),

                Select::make('empresa_id')
                    ->label('Empresa')
                    ->relationship('empresa', 'razon_social')
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
                Tables\Actions\Action::make('enviar_correo')
                    ->label('Enviar Confirmación')
                    ->icon('heroicon-o-mail')
                    ->visible(fn ($record) => self::validarComposicionAccionaria($record->id_empresa))
                    ->action(fn ($record) => self::enviarCorreoConfirmacion($record))
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function validarComposicionAccionaria($id_empresa)
    {
        $empresa = \App\Models\Empresa::find($id_empresa);
        if (!$empresa) return false;

        $accionistas = $empresa->accionistas()->get();

        foreach ($accionistas as $accionista) {
            if ($accionista->esPersonaJuridica() && !$accionista->tieneSoloPersonasNaturales()) {
                return false;
            }
        }
        return true;
    }

    public static function enviarCorreoConfirmacion($record)
    {
        Mail::raw('Hello World!', function($msg) {$msg->to('jonathan.garzon@realtechltda.com')->subject('Test Email'); });
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

