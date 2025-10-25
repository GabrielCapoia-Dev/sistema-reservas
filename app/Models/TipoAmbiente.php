<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoAmbiente extends Model
{
    protected $table = 'tipo_ambientes';


    protected $fillable = [
        'nome',
        'status',
    ];

    public function ambientes()
    {
        return $this->hasMany(Ambiente::class);
    }
}
