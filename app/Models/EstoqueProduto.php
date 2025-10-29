<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EstoqueProduto extends Pivot
{
    protected $table = 'estoque_produto';
    protected $fillable = [
        'produto_id',
        'estoque_id',
        'quantidade',
    ];

    protected $casts = [
        'quantidade' => 'integer',
    ];

    public $timestamps = true;

    protected static function booted()
    {
        static::saving(function ($pivot) {
            $pivot->quantidade = max(0, (int) $pivot->quantidade);
        });
    }
}
