<?php
App::import('Model', 'Unidade');
class UnidadeTestCase extends CakeTestCase {

/** CakeTestCase::start() */
	function start() {
		$this->Unidade	=& ClassRegistry::init('Unidade');
	}

/** CakeTestCase::end() */
	function end() {}

/**
 * Teste do método de retorno da lista de unidades.
 */
	function testFindList() {

		$unidades= $this->Unidade->findList(false, null);
		$this->assertEqual(sizeof($unidades), 472);
	}

/**
 * Teste do método obtenção da unidade a partir do nosso número.
 */
	function testFindByNossoNumero() {

		$this->Unidade->useDbConfig= 'default';

		$n_exemplo	= '0000002090709543';
		$u1 = $this->Unidade->findByNossoNumero($n_exemplo);

		$this->Unidade->contain();
		$u2 = $this->Unidade->find('first', array('conditions'=> array(
			  'and'=> array(
				  'Unidade.quadra_id'=> '02'
				, 'Unidade.lote'=> '09'
		))));

		$this->assertEqual($u1, $u2);
	}
}
