<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegraTributaria extends Model
{
    protected $fillable = [
        'produto_id',
        'ato_legal',
        'mva_original',
        'multiplicador_original',
        'mva_ajustada',
        'multiplicador_ajustado',
        'aliquota_interna',
        'aliquota_interestadual',
        'descricao_extra',
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
