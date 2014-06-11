<?php
class UsuariosController extends AppController {
	var $name		= 'Usuarios';
	var $helpers	= array('Javascript');

	function index() {

		$this->redirect(array('action'=> 'logout'));
	}

	function login() {

		$this->layout	= 'first';
		if( $this->Auth->user() ) {
			$this->redirect($this->Auth->loginRedirect);
		}
	}

	function logout() {
		$this->Auth->logout();
		$this->redirect($this->Auth->logoutRedirect);
	}

	function cadastrar() {

		if( !empty($this->data) ) {

			$hash1	= $this->data['Usuario']['senha'];
			$hash2	= $this->Auth->password($this->data['Usuario']['confirmacao']);
			if( $hash1 == $hash2 ) {

				if( $this->Usuario->save($this->data) ) {

					$this->Session->setFlash(__('Usuário cadastrado com sucesso.', true), 'flash_success');
					$this->redirect(array('controller'=> 'menus', 'action'=> 'index'));
				} else {

					$this->Session->setFlash(__('Oops. Não foi possível cadastrar o usuário.', true), 'flash_error');
				}
			}
		}
	}
}
