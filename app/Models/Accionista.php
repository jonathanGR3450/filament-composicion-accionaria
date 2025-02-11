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
}

