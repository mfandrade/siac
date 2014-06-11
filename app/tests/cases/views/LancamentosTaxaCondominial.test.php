<?php
require_once APP.'tests'.DS.'selenium_test_case.php';
class LancamentosTaxaCondominialTest extends SeleniumTestCase {

	function login() {
		$this->selenium->open('/siac/usuarios/logout');
		$this->selenium->type('UsuariosUsuario', 'admin');
		$this->selenium->type('UsuariosSenha', '123456');
		$this->selenium->click('btnAcessar');
		$this->selenium->waitForPageToLoad();
	}

	function testJan2010Sucesso() {
		$this->login();

		$this->selenium->open('/siac/menus');
		$this->selenium->click('link=Taxa Condominial Mensal');
		$this->selenium->select('LancamentoMesAno','label=01/2010');
		$this->selenium->click('LancamentoInstrucaoBoletoId1');
		$this->selenium->click('btnOkay');
		$this->selenium->waitForPageToLoad();
		$this->selenium->verifyTextPresent('Gerando Boletos...');
	}
}
