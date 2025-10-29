<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estoque extends Model
{
    protected $table = 'estoques';

    protected $fillable = [
        'nome',
        'descricao',
    ];

    public function produtos()
    {
        return $this->belongsToMany(Produto::class, 'estoque_produto', 'estoque_id', 'produto_id')
            ->using(\App\Models\EstoqueProduto::class)
            ->withPivot(['quantidade', 'created_at', 'updated_at'])
            ->withTimestamps();
    }
}
