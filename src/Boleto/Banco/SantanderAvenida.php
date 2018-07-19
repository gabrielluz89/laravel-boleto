<?php
namespace Eduardokum\LaravelBoleto\Boleto\Banco;

use Eduardokum\LaravelBoleto\CalculoDV;
use Eduardokum\LaravelBoleto\Util;

class SantanderAvenida extends Santander
{

    /**
     * Define os nomes das carteiras para exibição no boleto
     *
     * @var array
     */
    protected $carteirasNomes = ['101' => '101 - Cobrança Simples ECR', '102' => '102 - Cobrança Simples CSR', '201' => '201 - Penhor'];

    /**
     * Retorna o campo Agência/Beneficiário do boleto
     *
     * @return string
     */
    public function getAgenciaCodigoBeneficiario()
    {
        $agencia = $this->getAgenciaDv() !== null ? $this->getAgencia() . '-' . $this->getAgenciaDv() : $this->getAgencia();
        $conta = $this->getContaDv() !== null ? $this->getConta() . '-' . $this->getContaDv() : $this->getConta();
        return $agencia . ' / ' . $conta;
    }

}
