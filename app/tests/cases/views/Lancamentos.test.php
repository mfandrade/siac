<?php
App::import('model', 'Lancamento');
class LancamentosTaxaCondominialTest extends CakeWebTestCase {

/** Construtor.  Define onde está a homepage. */
	function LancamentosTaxaCondominialTest() {
		$this->baseurl	= current(split('app', $_SERVER['HTTP_REFERER']));
		
		$this->Lancamento	=& ClassRegistry::init('Lancamento');
		$this->Lancamento->useDbConfig	= 'test';
	}
	
/**
 * Verifica se estão presentes os campos: LancamentoMesAno(select),
 * LancamentoValorDocumento(text/disabled) e LancamentoInstrucaoBoleto(radio).
 */
	function testTaxaCondominial() {
		$this->assertTrue($this->get($this->baseurl . 'lancamentos/taxacondominial'));

		$mesAno	= $this->Lancamento->obterMesAnoDefaultLancamento(15, 7);
		$this->setField('data[Lancamento][mes_ano]', $mesAno);

		$valorDocumento	= Configure::read('lancamentos_taxacondominial_valor');
		$valorDocumento	= number_format($valorDocumento, 2, ',', '');
		$this->setField('data[Lancamento][valor_documento]', $valorDocumento);

		$this->setField('data[Lancamento][instrucao_boleto_id]', 1);
		
		
		$this->clickSubmitById('btnSubmit');
	}
}
