<?php
namespace App\Filament\Resources;

use App\Filament\Resources\AccionistaResource\Pages;
use App\Models\Accionista;
use App\Models\Empresa;
use App\Models\TipoPersona;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
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
                    ->options(function () {
                        return Accionista::where('tipo_persona_id', TipoPersona::TIPO_PERSONA_JURIDICA) // Solo personas jurídicas
                            ->where('empresa_id', Auth::user()->empresa_id) // De la misma empresa del usuario
                            ->pluck('nombre', 'id');
                    })
                    ->nullable(),

                Hidden::make('empresa_id')
                    ->default(fn () => Auth::user()->empresa_id),

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
                TextColumn::make('nombre')->label('Nombre / Razón Social')->sortable(),
                TextColumn::make('tipoPersona.nombre')->label('Tipo de Persona'),
                TextColumn::make('numero_identificacion')->label('Identificación'),
                TextColumn::make('participacion_accionaria')->label('Participación %'),
                // TextColumn::make('empresa.razon_social')->label('Empresa'),
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
            ])
            ->headerActions([
                Tables\Actions\Action::make('enviar_correo')
                    ->label('Enviar Confirmación')
                    ->icon('heroicon-o-envelope')
                    ->visible(fn () => self::validarComposicionAccionaria(Auth::user()->empresa_id))
                    ->action(fn () => self::enviarCorreoConfirmacion(Auth::user()->empresa_id))
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function validarComposicionAccionaria($empresa_id)
    {
        $empresa = Empresa::find($empresa_id);

        if (!$empresa) return false;

        $accionistas = $empresa->accionistas()->whereNull('id_padre')->get();

        if (self::validarEstructuraAccionaria($accionistas)) {
            return true;
        }

        return false;
    }

    /**
    * Función recursiva para validar la estructura accionaria
    */
    private static function validarEstructuraAccionaria($accionistas, $nivel = 1)
    {
        $totalParticipacion = 0;
        // echo "Nivel: " . $nivel . "\n";
        // echo "<pre>";
        // print_r($accionistas->toArray());
        // echo "</pre>";

        foreach ($accionistas as $accionista) {
            $totalParticipacion += $accionista->participacion_accionaria;

            if ($accionista->esPersonaJuridica()) {
                $subAccionistas = $accionista->accionistasHijos()->get();

                if ($subAccionistas->isEmpty()) {
                    return false; // Tiene una persona jurídica sin estructura detallada
                }

                // Validar recursivamente la composición accionaria de la persona jurídica
                if (!self::validarEstructuraAccionaria($subAccionistas, $nivel + 1)) {
                    return false;
                }
            }
        }

        // La sumatoria de la participación debe ser 100%
        return round($totalParticipacion, 2) === 100.00;
    }


    public static function enviarCorreoConfirmacion($record)
    {
        Mail::raw('Hello World!', function($msg) {
            $msg->to('jonathan.garzon@realtechltda.com')->subject('Test Email');
        });
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

    public static function canCreate(): bool
    {
        return Auth::user() !== null;
    }
}

