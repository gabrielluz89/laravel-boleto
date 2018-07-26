<?php

namespace Eduardokum\LaravelBoleto\Boleto\Render;

use Eduardokum\LaravelBoleto\Util;
use Eduardokum\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Eduardokum\LaravelBoleto\Contracts\Boleto\Render\Pdf as PdfContract;

class PdfAvenida extends Pdf
{
    /**
     * função para gerar o boleto
     *
     * @param string $dest tipo de destino const BOLETOPDF_DEST_STANDARD | BOLETOPDF_DEST_DOWNLOAD | BOLETOPDF_DEST_SAVE | BOLETOPDF_DEST_STRING
     * @param null $save_path
     *
     * @return string
     * @throws \Exception
     */

    protected function montarEstrutura() {
        for ($i = 0; $i < $this->totalBoletos; $i++) {
            $this->SetDrawColor('0', '0', '0');
            $this->AddPage();
            $this->demonstrativo($i)->instrucoes($i)->logoEmpresa($i)->Topo($i, false);
            $this->AddPage();
            $this->Bottom($i)->codigoBarras($i);
        }
    }

    protected function demonstrativo($i)
    {
        if($this->boleto[$i]->getShowDemonstrativo()) {
            $this->SetFont($this->PadraoFont, '', 6);
            $this->Cell(0, $this->desc, $this->_('Prezado(a) cliente '.$this->boleto[$i]->getDemonstrativoInfo('nome_cliente')), 0, 1, 'L');
            $this->Cell(0, $this->desc, $this->_('Conforme a negociação formalizada com o '.$this->boleto[$i]->getDemonstrativoInfo('fantasia').' em '.$this->boleto[$i]->getDemonstrativoInfo('data_negociacao').', seguem as instruções para pagamento nas lojas.'), 0, 1, 'L');
            $this->Cell(0, $this->desc, $this->_(''.$this->boleto[$i]->getDemonstrativoInfo('nome_loja').', referente ao pagamento da parcela do acordo citado abaixo com vencimento em '.$this->boleto[$i]->getDemonstrativoInfo('vencimento').' no valor de R$ '.$this->boleto[$i]->getDemonstrativoInfo('valor_parcela')), 0, 1, 'L');
            $this->Ln(2);
            $this->Cell(0, $this->desc, $this->_('Acordo referente ao(s) contrato(s): '.$this->boleto[$i]->getDemonstrativoInfo('contratos')), 0, 1, 'L');
            $this->Ln(2);
            $this->SetFont($this->PadraoFont, '', $this->fcel);
            $this->Cell(40, $this->cell, $this->_('Entrada: '), 0, 0);
            $this->SetFont($this->PadraoFont, 'B', $this->fcel);
            $this->Cell(0, $this->cell, $this->_('R$ '.$this->boleto[$i]->getDemonstrativoInfo('valor_entrada')), 0, 1);
            $this->SetFont($this->PadraoFont, '', $this->fcel);
            $this->Cell(40, $this->cell, $this->_('Vencimento: '), 0, 0);
            $this->SetFont($this->PadraoFont, 'B', $this->fcel);
            $this->Cell(0, $this->cell, $this->_($this->boleto[$i]->getDemonstrativoInfo('vencimento')), 0, 1);
            $this->SetFont($this->PadraoFont, '', $this->fcel);
            $this->Cell(40, $this->cell, $this->_('Quantidade de parcelas'), 0, 0);
            $this->SetFont($this->PadraoFont, 'B', $this->fcel);
            $this->Cell(0, $this->cell, $this->_($this->boleto[$i]->getDemonstrativoInfo('parcela')), 0, 1);
            $this->SetFont($this->PadraoFont, '', $this->fcel);
            $this->Cell(40, $this->cell, $this->_('Valor de cada parcela'), 0, 0);
            $this->SetFont($this->PadraoFont, 'B', $this->fcel);
            $this->Cell(0, $this->cell, $this->_('R$ '.$this->boleto[$i]->getDemonstrativoInfo('valor_parcelas')), 0, 1);
            $this->SetFont($this->PadraoFont, '', $this->fcel);
            $this->Cell(40, $this->cell, $this->_('Vencimentos'), 0, 0);
            $this->SetFont($this->PadraoFont, 'B', $this->fcel);
            $this->Cell(0, $this->cell, $this->_($this->boleto[$i]->getDemonstrativoInfo('vencimentos')), 0, 1);
            $this->SetFont($this->PadraoFont, '', $this->fcel);
            $this->Ln(2);
            $this->Cell(0, $this->desc, $this->_('Este acordo conetempla unicamente a(s) parcela(s) abaixo relacionada(s)'), 0, 1, 'L');
            $this->Ln(2); 
            $this->Ln(2); 

            $this->SetFont($this->PadraoFont, 'B', $this->fcel);
            $this->Cell(40, $this->cell, $this->_('Parcela'), 0, 0);
            $this->Cell(40, $this->cell, $this->_('Vencimento'), 0, 0);
            $this->Cell(40, $this->cell, $this->_('Valor'), 0, 0);
            $this->Cell(40, $this->cell, $this->_('Situação'), 0, 1);

            $this->Ln(2); 
            $this->SetFont($this->PadraoFont, '', $this->fcel);
            $this->Cell(40, $this->cell, $this->_($this->boleto[$i]->getDemonstrativoInfo('parcela')), 0, 0);
            $this->Cell(40, $this->cell, $this->_($this->boleto[$i]->getDemonstrativoInfo('vencimento')), 0, 0);
            $this->Cell(40, $this->cell, $this->_("R$ ".$this->boleto[$i]->getDemonstrativoInfo('valor_parcela')), 0, 0);
            $this->Cell(40, $this->cell, $this->_($this->boleto[$i]->getDemonstrativoInfo('situacao')), 0, 1);

            $this->Ln(4); 
            // $this->traco('', 0, 10);
        }

        return $this;
    }

    /**
     * @param integer $i
     *
     * @return $this
     */
    protected function logoEmpresa($i)
    {
        $this->Ln(2);
        $this->SetFont($this->PadraoFont, '', $this->fdes);

        $logo = preg_replace('/\&.*/', '', $this->boleto[$i]->getLogo());
        $ext = pathinfo($logo, PATHINFO_EXTENSION);

        $this->Image($this->boleto[$i]->getLogo(), 20, ($this->GetY()), 0, 12, $ext);
        $this->Cell(90);
        $this->Cell(0, $this->desc, $this->_($this->boleto[$i]->getBeneficiario()->getNome()), 0, 1);
        $this->Cell(90);
        $this->Cell(0, $this->desc, $this->_($this->boleto[$i]->getBeneficiario()->getDocumento(), '##.###.###/####-##'), 0, 1);
        $this->Cell(90);
        $this->Cell(0, $this->desc, $this->_($this->boleto[$i]->getBeneficiario()->getEndereco()), 0, 1);
        $this->Cell(90);
        $this->Cell(0, $this->desc, $this->_($this->boleto[$i]->getBeneficiario()->getCepCidadeUf()), 0, 1);
        $this->Ln(8);

        return $this;
    }


}
