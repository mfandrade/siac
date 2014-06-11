<?php
App::import('Controller', 'Lancamentos');
Mock::generate('LancamentosController');
class LancamentosControllerTest extends CakeTestCase {

	function startCase() {
		echo '<h1>INÍCIO DO CASO DE TESTE</h1>';
		$this->Lancamentos	=& new MockLancamentosController();
	}
	function endCase() {
		echo '<h1>FINAL DO CASO DE TESTE</h1>';
	}
	function startTest($method) {
		echo '<h1>Método: '.$method.'</h1>';
	}
	function endTest($method) {
		echo '<hr />';
	}


	function testTaxaCondominialHtml() {
		//$result	= $this->testAction('/lancamentos/taxacondominial', array('return'=> 'render'));
		//debug($result);
	}

	function testTaxaCondominialVars() {
		$this->Lancamentos->taxacondominial();
		pr($this->Lancamentos->viewVars); exit;
	}

	function testTaxaCondominialFixturize() {
		$result	= $this->testAction('/lancamentos/taxacondominial', array('fixturize'=> true));
		debug($result);
	}
}
