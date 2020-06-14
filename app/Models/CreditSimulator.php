<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditSimulator extends Model
{

    const VALOR = 'valor';
    const PARCELAS = 'parcelas';
    const INSTITUICOES = 'instituicoes';
    const CONVENIOS = 'convenios';
    

    public $valor;
    public $parcelas;
    public $instituicoes;
    public $convenios;

    public function format($simulator, $payload) {
    	!($payload->valor) ?: $simulator->valor = $payload->valor;
    	!($payload->parcelas) ?: $simulator->parcelas = $payload->parcelas;
    	!($payload->instituicoes) ?: $simulator->instituicoes = $payload->instituicoes;
    	!($payload->convenios) ?: $simulator->convenios = $payload->convenios;
    }
}
