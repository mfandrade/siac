<?php
class UnidadesController extends AppController {
	var $name		= 'Unidades';
	var $paginate	= array(
		  'contain'	=> array('Proprietario')
		, 'order'	=> array('Unidade.quadra_id'=> 'ASC', 'Unidade.lote'=> 'ASC')
	);
	var $helpers	= array('Formatar', 'Navigator', 'Javascript', 'Ajax');

/** CRUD. */
	function index($id= null) {

		$unidades		= $this->Unidade->findList();
		$unidades[0]	= __('SELECIONE', true);
		$this->set(compact('unidades', 'id'));

		if( !empty($this->data) ) {

			if( !empty($this->data['Unidade']['id']) ) {

				$this->redirect(array('action'=> 'ver', $this->data['Unidade']['id']));
			} else {

				$this->Session->setFlash(__('Por favor, selecione uma unidade.', true), 'flash_warning');
			}
		}
	}

/** CRUD. */
	function ver($id) {

		$this->Unidade->contain(array('Proprietario', 'Quadra', 'Rua'));
		$this->Unidade->id	= $id;
		$unidade	= $this->Unidade->read();

		if( $this->Session->check('Unidades.ver_unidades') ) {
			$unidades	= $this->Session->read('Unidades.ver_unidades');
		} else {
			$this->Unidade->contain('Quadra');
			$unidades	= $this->Unidade->find('all', array('order'=> array('Unidade.quadra_id', 'Unidade.lote')));
		}
		$vizinhos	= $this->Unidade->findNeighbours($unidade, $unidades);
		$this->Unidade->Proprietario->contain();
		$proprietarios	= $this->Unidade->Proprietario->find('list', array('order'=> 'Proprietario.nome'));

		$this->set(compact('unidade', 'proprietarios', 'vizinhos'));
	}

	function index_() {

		$this->paginate['limit']	= Configure::read('siac_listagem_qtdregistros');
		$unidades	= $this->paginate('Unidade');
		$this->set(compact('unidades'));
	}

/**
 * Método CRUD.
 * @param  int $id  id da unidade a editar
 * @return void
 */
	function editar($id) {

		if( empty($this->data) ) {

			$this->Unidade->id	= $id;
			$this->data			= $this->Unidade->read();
		} else {

			if( $this->Unidade->save($this->data) ) {

				$this->_flash(__('Dados da unidade atualizados com sucesso'), 'success');
				$this->redirect(array('controller'=> 'unidades', 'action'=> 'index'));
			} else {

				$this->_flash(__('Oops. Não foi possível atualizar os dados da unidade'), 'error');
			}
		}
		$this->set('proprietarios', $this->Unidade->Proprietario->find('list', array('order'=> 'Proprietario.nome')));
	}

/**
 *
 */
	function ajaxatualizarproprietario($unidade_id= null) {

		if( !$this->RequestHandler->isAjax() ) {
			$this->redirect(array('controller'=> 'menus', 'action'=> 'index'));
		}
		if( !empty($this->params) && isset($unidade_id) ) {

			$this->Unidade->contain();
			$this->Unidade->id	= $unidade_id;
			$unidade	= $this->Unidade->read();
/*			$antigo		= $unidade['Unidade']['proprietario_id'];
			// verifica se o proprietário não é dono de mais unidades
			$qtd	= $this->Unidade->find('count', array('conditions'=> array('Unidade.proprietario_id'=> $antigo)));
			if( $qtd == 1 ) {
				// TODO: exclui logicamente?
			}
*/
			$this->Unidade->Proprietario->contain();
			$proprietario	= $this->Unidade->Proprietario->find('first', array(
				  'conditions'=> array(
					  'Proprietario.nome'=> $this->params['form']['value']
				)
				, 'order'=> 'Proprietario.nome'
			));
			$id		= $proprietario['Proprietario']['id'];
			$antigo	= $unidade['Unidade']['proprietario_id'];
			if( $id != $antigo ) {
				$this->Unidade->updateAll(array('Unidade.proprietario_id'=> $id), array('Unidade.id'=> $unidade_id));
			}
			$this->set('nome', $proprietario['Proprietario']['nome']);
		}
	}
}
