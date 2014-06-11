<?php
class InstrucoesBoletoController extends AppController {
	var $name		= 'InstrucoesBoleto';
	var $uses		= 'InstrucaoBoleto';
	var $helpers	= array('Formatar', 'Navigator');
	var $paginate	= array(
		  'contain'	=> array('TipoLancamento')
		, 'order'	=> array('InstrucaoBoleto.tipo_lancamento_id'=> 'ASC')
	);
	var $scaffold;

/** 
 * Método CRUD.
 */
/*	function index() {
		
		$this->paginate['limit']	= Configure::read('siac_listagem_qtdregistros');
		$instrucoes	= $this->paginate('InstrucaoBoleto');
		$this->set(compact('instrucoes'));
	}
*/
}
