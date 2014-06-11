<?php
class AppController extends Controller {
	var $components		= array('RequestHandler', 'Auth');
	var $persistModel	= false;
	var $layout			= 'default';

/** Controller::beforeFilter() */
	function beforeFilter() {

		parent::beforeFilter();

		// configurações
		Configure::load('siac.config');

		// ajax
		if( $this->RequestHandler->isAjax() ) {
			Configure::write('debug', 0);
		}

		// login
		Security::setHash('sha1');
		
		//$this->Auth->allow('cadastrar');

		$this->Auth->userModel	= 'Usuario';
		$this->Auth->fields		= array('username'=> 'usuario', 'password'=> 'senha');
		$this->Auth->authorize	= 'controller';

		$this->Auth->autoRedirect	= false;
		$this->Auth->loginAction	= array('controller'=> 'usuarios', 'action'=> 'login');
		$this->Auth->loginRedirect	= array('controller'=> 'menus', 'action'=> 'index');
		$this->Auth->logoutRedirect	= array('controller'=> 'usuarios', 'action'=> 'login');

		$this->Auth->loginError		= __('Usuário/senha não conferem', true);
		$this->Auth->authError		= __('Por favor, autentique-se novamente', true);
	}

/** AuthComponent::isAuthorized() */
	function isAuthorized() {

		return true;
	}
}
