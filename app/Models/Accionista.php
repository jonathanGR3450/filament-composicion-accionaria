<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accionista extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_persona_id',
        'numero_identificacion',
        'nombre',
        'participacion_accionaria',
        'id_padre',
        'empresa_id',
        'estado'
    ];

    public function tipoPersona()
    {
        return $this->belongsTo(TipoPersona::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function accionistasHijos()
    {
        return $this->hasMany(Accionista::class, 'id_padre');
    }

    public function accionistaPadre()
    {
        return $this->belongsTo(Accionista::class, 'id_padre');
    }
    public function esPersonaNatural()
    {
        return $this->tipoPersona->nombre === 'Natural';
    }

    public function esPersonaJuridica()
    {
        return $this->tipoPersona->nombre === 'Jurídica';
    }

    public function tieneSoloPersonasNaturales()
    {
        return $this->accionistasHijos()->count() > 0 && $this->accionistasHijos()->whereHas('tipoPersona', function ($query) {
            $query->where('nombre', 'Jurídica');
        })->count() === 0;
    }

    public static function validarComposicionAccionaria($id_empresa)
    {
        $empresa = Empresa::find($id_empresa);
        $accionistas = $empresa->accionistas()->get();

        foreach ($accionistas as $accionista) {
            if ($accionista->esPersonaJuridica() && !$accionista->tieneSoloPersonasNaturales()) {
                return false;
            }
        }
        return true;
    }
}
