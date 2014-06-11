<?php
App::import('Model', 'Pagamento');
class PagamentoTestCase extends CakeTestCase {

/** CakeTestCase::start() */
	function start() {
		$this->Pagamento	=& ClassRegistry::init('Pagamento');
		$this->Pagamento->useDbConfig	= 'test';
		$this->Pagamento->deleteAll(true);
		$this->Pagamento->Lancamento->deleteAll(true);
		$this->Pagamento->Lancamento->query('ALTER TABLE lancamentos AUTO_INCREMENT 0');
	}

/** CakeTestCase::end() */
	function end() {}

/**
 * Teste para o método de validação Pagamento::totalizar.
 */
	function testValidacaoTotalizar() {

		$this->Pagamento->data['Pagamento']['valor_documento']	= 110.11;
		$this->Pagamento->data['Pagamento']['valor_acrescimo']	= 2;
		$this->Pagamento->data['Pagamento']['valor_desconto']	= 1;
		$this->Pagamento->data['Pagamento']['valor_pago']		= 111.11;

		$true	= $this->Pagamento->totalizar(
			  $this->Pagamento->data['Pagamento']['valor_pago']
			, array('valor_documento', 'valor_desconto', 'valor_acrescimo'));
		$this->assertEqual($true, true);


		$this->Pagamento->data['Pagamento']['valor_desconto']	= 0;

		$false	= $this->Pagamento->totalizar(
			  $this->Pagamento->data['Pagamento']['valor_pago']
			, array('valor_documento', 'valor_desconto', 'valor_acrescimo'));
		$this->assertEqual($false, false);
	}

/**
 * Teste para o método de validação Pagamento::naoRetroativa.
 */
	function testValidacaoNaoRetroativa() {

		$dta_pagamento	= '2008-09-15';
		$hoje			= date('Y-m-d');

		$false	= $this->Pagamento->naoRetroativa($dta_pagamento);
		$this->assertEqual($false, false);

		$true	= $this->Pagamento->naoRetroativa($hoje);
		$this->assertEqual($true, true);
	}

/**  */
	function testCalcularValorAcrescimo() {

		$valor	= 300.00;

		// 1, pagamento antes do vencimento
		$calculado	= $this->Pagamento->calcularValorAcrescimo($valor, '15/09/2009', '10/09/2009');
		$esperado['valor_multa']	= 0.00;
		$esperado['valor_juros']	= 0.00;
		$esperado['valor_desconto']	= Configure::read('lancamentos_taxacondominial_valor_descontopadrao');
		$esperado['atraso']			= 0;
		$this->assertEqual($calculado, $esperado);

		// 2, pagamento no dia do vencimento
		$calculado	= $this->Pagamento->calcularValorAcrescimo($valor, '15/09/2009', '15/09/2009');
		$esperado['valor_multa']	= 0.00;
		$esperado['valor_juros']	= 0.00;
		$esperado['valor_desconto']	= Configure::read('lancamentos_taxacondominial_valor_descontopadrao');
		$esperado['atraso']			= 0;
		$this->assertEqual($calculado, $esperado);

		// 3, pagamento depois do vencimento, dentro do mês
		$calculado	= $this->Pagamento->calcularValorAcrescimo($valor, '15/09/2009', '16/09/2009');
		$esperado['valor_multa']	= number_format($valor * Configure::read('pagamentos_atraso_porcentagem_multa') / 100, 2);
		$esperado['valor_juros']	= number_format($valor * Configure::read('pagamentos_atraso_porcentagem_juros_ad') / 100, 2);
		$esperado['valor_desconto']	= $esperado['valor_multa'] + $esperado['valor_juros'];
		$esperado['atraso']			= 1;
		$this->assertEqual($calculado, $esperado);

		// 4, pagamento depois do vencimento de depois do mês
		$calculado	= $this->Pagamento->calcularValorAcrescimo($valor, '15/09/2009', '15/10/2009');
		$esperado['atraso']			= 30;
		$esperado['valor_multa']	= number_format($valor * Configure::read('pagamentos_atraso_porcentagem_multa') / 100, 2);
		$esperado['valor_juros']	= number_format($valor * Configure::read('pagamentos_atraso_porcentagem_juros_ad') * $esperado['atraso'] / 100, 2);
		$esperado['valor_desconto']	= 0.00;
		$this->assertEqual($calculado, $esperado);

	}
}
