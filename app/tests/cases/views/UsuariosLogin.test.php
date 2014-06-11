<?php
require_once APP.'tests'.DS.'selenium_test_case.php';
class UsuariosCadastrarTest extends SeleniumTestCase {

	function testUsuarioAdmin123456Sucesso() {
		$this->selenium->open('/');
		$this->selenium->type('UsuariosUsuario', 'admin');
		$this->selenium->type('UsuariosSenha', '123456');
		$this->selenium->click('btnAcessar');
		$this->assertTrue($this->selenium->getTitle('SIAC / Menus'));

		$this->selenium->open('/usuarios/logout');
	}

	function testUsuarioBrancoFalha() {
		$this->selenium->open('/');
		$this->selenium->type('UsuariosUsuario', '');
		$this->selenium->type('UsuariosSenha', 'LIXO');
		$this->selenium->click('btnAcessar');
		$this->assertTrue($this->selenium->getText('Usuário/senha não conferem'));
	}

	function testSenhaBrancoFalha() {
		$this->selenium->open('/');
		$this->selenium->type('UsuariosUsuario', 'blablabla');
		$this->selenium->type('UsuariosSenha', '');
		$this->selenium->click('btnAcessar');
		$this->assertTrue($this->selenium->getText('Usuário/senha não conferem'));
	}

	function testSqlInjectionSimplesFalha() {
		$this->selenium->open('/');
		$this->selenium->type('UsuariosUsuario', '\' OR 1=1 --');
		$this->selenium->type('UsuariosSenha', '');
		$this->selenium->click('btnAcessar');
		$this->assertTrue($this->selenium->getText('Usuário/senha não conferem'));
	}

/*	function testUsuarioMuuuitoLongoFalha() {

		$DEZ_MILHOES	= 9999999;
		for( $i= 0; $i < $DEZ_MILHOES; $i++ ) {
			$login.= 'a';
		}

		$this->selenium->open('/');
		$this->selenium->type('UsuariosUsuario', $login);
		$this->selenium->type('UsuariosSenha', '');
		$this->selenium->click('btnAcessar');
		$this->assertTrue($this->selenium->getText('Usuário/senha não conferem'));
	}
*/
}
