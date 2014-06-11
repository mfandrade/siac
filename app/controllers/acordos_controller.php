<?php
class AcordosController extends AppController {
	var $name		= 'Acordos';
	var $helpers	= array('Javascript', 'Ajax', 'Formatar');

/**
 * Action correspondente à geração de novo acordo.
 */
	function cadastrar() {

		$unidades	= $this->Acordo->Lancamento->Unidade->findList();
		$unidades[0]= __('SELECIONE', true);
		$hoje		= date('d/m/Y');
		$this->set(compact('unidades', 'hoje'));
	}

/**
 * Retorna, via ajax, os meses e anos em aberto referentes à unidade dada,
 * prontos para serem selecionados para definição do acordo.
 */
	function ajaxobterlancamentosaberto() {

		if( !$this->RequestHandler->isAjax() ) {
			$this->redirect(array('controller'=> 'menus', 'action'=> 'index'));
		}
		if( !empty($this->data) ) {

			if( empty($this->data['Acordo']['unidade_id']) ) {
				$this->redirect(array('controller'=> 'menus', 'action'=> 'index'));
			}
			$unidade_id	= $this->data['Acordo']['unidade_id'];

			$tipos		= array(
				  TIPO_LANCAMENTO_TAXACONDOMINIAL	=> __('Taxa Condominial', true)
				, TIPO_LANCAMENTO_TAXAEXTRA			=> __('Taxa Extra', true)
				, TIPO_LANCAMENTO_MULTAINFRACAO		=> __('Multa por Infração', true)
			);
			$emAberto	= array();
			foreach( $tipos as $tipo=> $descricao ) {

				$lancamentos	= $this->Acordo->Lancamento->obterLancamentosAberto($unidade_id, null, $tipo);
				if( empty($lancamentos) ) continue;
				$t				= $tipos[$tipo];
				$emAberto[$t]	= Set::combine($lancamentos, '{n}.Lancamento.id', array('%s|%.2f|%.2f|%.2f|%.2f|%d', '{n}.Lancamento.mes_ano', '{n}.Lancamento.valor_documento', '{n}.Pagamento.valor_juros', '{n}.Pagamento.valor_multa', '{n}.Pagamento.valor_desconto', '{n}.Pagamento.atraso'));
				asort($emAberto[$t]);
			}
			$this->set(compact('emAberto'));
		}
	}
}
