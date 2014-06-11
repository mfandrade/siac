<?php
class ProprietariosController extends AppController {
	var $name		= 'Proprietarios';
	var $paginate	= array(
		  'contain'	=> array()
		, 'order'	=> array('Proprietario.nome'=> 'ASC')
	);
	var $helpers	= array('Formatar');

/**
 * Método ajax para autocompletar uma listagem de proprietários.
 */
	function ajaxlistarproprietarios() {

		if( !$this->RequestHandler->isAjax() ) {
			$this->redirect(array('controller'=> 'menus', 'action'=> 'index'));
		}
		if( !empty($this->data['Proprietario']['nome']) ) {

			$this->Proprietario->contain(array('Unidade'=>'Quadra'));
			$nome			= mb_strtoupper($this->data['Proprietario']['nome'], 'utf-8');
			$proprietarios	= $this->Proprietario->find('all', array(
				  'conditions'	=> array('Proprietario.nome LIKE'=> '%'.$nome.'%')
				, 'fields'		=> array('Proprietario.nome')
				, 'order'		=> array('Proprietario.nome ASC')
			));
			$this->set(compact('proprietarios', 'nome'));
		}
	}

/**
 * CRUD. Criar novo proprietário.
 */ 
	function cadastrar() {
		
		if( !empty($this->data) ) {
			if( $this->Proprietario->save($this->data)) {
				$this->Session->setFlash(__(sprintf('%s, de CPF/CNPJ %s cadastrado com sucesso', $this->data['Proprietario']['nome'], $this->data['Proprietario']['cpf_cnpj']), true), 'flash_success');
				$this->redirect(array('controller'=> 'menus', 'action'=> 'sistema_dadosauxiliares'));
			} else {
				$this->Session->setFlash(__('Algum problema ocorreu. O novo proprietário não foi cadastrado, tente novamente.', true), 'flash_error');
			}
		}
	}

/**
 * CRUD.
 */ 
	function editar($id) {
		$this->Proprietario->id = $id;
		$proprietario = $this->Proprietario->read();
		$this->set(compact('proprietario'));
	}
}
