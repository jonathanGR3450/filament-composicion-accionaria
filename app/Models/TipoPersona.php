<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoPersona extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion'];

    const TIPO_PERSONA_NATURAL = 1;
    const TIPO_PERSONA_JURIDICA = 2;

    public function accionistas()
    {
        return $this->hasMany(Accionista::class, 'tipo_persona_id');
    }
}
