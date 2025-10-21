<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    protected $fillable = ['secao_id', 'item', 'cest', 'ncm_sh', 'descricao'];

    public function secao() { return $this->belongsTo(Secao::class); }

    public function regrasTributarias() { return $this->hasMany(RegraTributaria::class); }
}
