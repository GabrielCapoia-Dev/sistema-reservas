<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    protected $table = 'produtos';

    protected $fillable = [
        'nome',
        'preco',
        'categoria_id',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function estoques()
    {
        return $this->belongsToMany(Estoque::class, 'estoque_produto', 'produto_id', 'estoque_id')
            ->using(\App\Models\EstoqueProduto::class)
            ->withPivot(['quantidade', 'created_at', 'updated_at'])
            ->withTimestamps();
    }
}
