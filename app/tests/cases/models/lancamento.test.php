<?php
App::import('Model', 'Lancamento');
class LancamentoTestCase extends CakeTestCase {

/** CakeTestCase::start() */
	function start() {
		$this->Lancamento	=& ClassRegistry::init('Lancamento');
		$this->Lancamento->useDbConfig	= 'test';
		$this->Lancamento->deleteAll('1=1');
	}

/** CakeTestCase::end() */
	function end() {
	}

/**
 * Teste para o método de validação Lancamento::noDuplicates.
 */
	function testValidacaoNoDuplicates() {

		$this->assertFalse(true, 'FALTA IMPLEMENTAR O TESTE DA VALIDAÇÃO "noDuplicats".');
	}

/**
 * Teste para o método Lancamento::obterMesAnoDefaultLancamento.
 */
	function testObterMesAnoDefaultLancamento() {

		$dia			= date('t');
		$antecedencia	= 0;

		$resultado	= $this->Lancamento->obterMesAnoDefaultLancamento($dia, $antecedencia);
		$esperado	= str_pad(date('m'), 2, '0', STR_PAD_LEFT) . '/' . date('Y');

		$this->assertEqual( $resultado, $esperado );
	}

/**
 * Teste para o método Lancamento::obterMesesAnosLancamento.
 */
	function testObterMesesAnosLancamento() {

		$dia			= 15;
		$antecedencia	= 0;
		$resultado	= $this->Lancamento->obterMesesAnosLancamento($dia, $antecedencia, 3);

		$mes1		= date('m/Y');
		$mes2		= date('m/Y', strtotime('+1 month'));
		$mes3		= date('m/Y', strtotime('+2 months'));
		$esperado	= array($mes1=> $mes1, $mes2=> $mes2, $mes3=> $mes3);

		$this->assertEqual( $resultado, $esperado );
	}

/**
 * Teste para o método Lancamento::obterMesesAnosLancados.
 */
	function testObterMesesAnosLancados() {

		$opcoes	= $this->Lancamento->obterMesesAnosLancados(TIPO_LANCAMENTO_TAXACONDOMINIAL, );
		$this->assert
	}


/**
 * Teste para o método de validação Lancamento::efetuarLancamentos.
 */
	function testEfetuarLancamentosTaxaCondominial() {

		$antes	= $this->Lancamento->find('list');
		$this->assertEqual( $antes, array() );


		$mock	= array(
			'Lancamento'	=> array(
				  'mes_ano'				=> '07/2009'
				, 'valor_documento'		=> 280.00
				, 'instrucao_boleto_id'	=> 1
				, 'tipo_lancamento_id'	=> 0 //$this->Lancamento->TipoLancamento::TAXACONDOMINIAL
				, 'usuario_id'			=> 1
		));
		$fez	= $this->Lancamento->efetuarLancamentos($mock);
		$this->assertEqual( $fez, true );


		$this->Lancamento->contain();
		$lancados	= $this->Lancamento->find('all', array(
			  'fields'		=> array(
				  'mes_ano', 'valor_documento', 'instrucao_boleto_id', 'tipo_lancamento_id', 'usuario_id'
			)
			, 'conditions'	=> array(
				'and'	=> array(
					  'Lancamento.mes_ano'				=> $mock['Lancamento']['mes_ano']
					, 'Lancamento.tipo_lancamento_id'	=> $mock['Lancamento']['tipo_lancamento_id']
				)
			)
		));
		$this->assertEqual( $mock, $lancados[0] );
	}

/**
 * Teste para o método de validação Lancamento::obterLancamentosAberto.
 */
	function testObterLancamentosAberto() {
	}
}
