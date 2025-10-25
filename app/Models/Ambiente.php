<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ambiente extends Model
{
    protected $table = 'ambientes';

    protected $fillable = [
        'tipo_ambiente_id',
        'nome',
        'status',
        'capacidade',
    ];

    public function tipoAmbiente()
    {
        return $this->belongsTo(TipoAmbiente::class);
    }
}
