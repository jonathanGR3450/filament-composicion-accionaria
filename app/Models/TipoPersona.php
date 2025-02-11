<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoPersona extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion'];

    public function accionistas()
    {
        return $this->hasMany(Accionista::class, 'tipo_persona_id');
    }
}
