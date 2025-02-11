<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = ['razon_social', 'nit'];

    public function accionistas()
    {
        return $this->hasMany(Accionista::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
