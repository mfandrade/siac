<?php
class UsuariosTest extends CakeWebTestCase {

/** Construtor.  Define onde está a homepage. */
	function UsuariosTest() {
		$this->homepage	= current(split('app', $_SERVER['HTTP_REFERER']));
	}

/** CakeWebTestCase::setUp() */
	function startTest() {
		$this->get($this->homepage.'usuarios/logout');
	}

/** Testa se a homepage está no ar. */
	function testHomePage() {
		$this->assertTrue($this->get($this->homepage));
		$this->assertTitle('SIAC - Sistema Integrado de Administração Condominial');
	}

/** Login. Testa se os campos UsuarioUsuario e UsuarioSenha existem. */
	function testLogin() {
		$this->get($this->homepage);
		$this->assertFieldById('UsuarioUsuario');
		$this->assertFieldById('UsuarioSenha');
	}

/** Login. Teste que falha. */
	function testLoginFailure() {
		$browser	=& new SimpleBrowser();
		$browser->get($this->homepage);
		$browser->clickSubmit('Acessar');
		$this->assertText(utf8_decode('Usuário/senha não conferem'));

		unset($browser);
		$browser	=& new SimpleBrowser();
		$browser->get($this->homepage);
		$browser->setField('Usuário:', 'zeninguem');
		$browser->click('Acessar');
		$this->assertText(utf8_decode('Usuário/senha não conferem'));
	}

/** Login. Teste que procede. */
	function testLoginSuccess() {
	}
}
