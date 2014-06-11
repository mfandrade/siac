<?php
class PagamentosController extends AppController {
	var $name		= 'Pagamentos';
	var $helpers	= array('Javascript', 'Ajax', 'Formatar');

/**
 * Action.
 */
	function index() {

		$this->redirect(array('controller'=> 'menus', 'action'=> 'pagamentos'));
	}

/**
 * Action que trata pagamentos via arquivo de retorno.
 */
	function arquivoretorno() {

		$this->Pagamento->ArquivoRetorno->contain();
		$ultimo	= $this->Pagamento->ArquivoRetorno->find('first', array(
			  'conditions'=> 'ArquivoRetorno.processado'
			, 'order'=> 'created DESC'
		));
		if( $ultimo ) {

			$arquivo= $ultimo['ArquivoRetorno']['arquivo'];
			$data	= $ultimo['ArquivoRetorno']['dta_gravacao'];
		} else {

			$arquivo= __('NENHUM', true);
			$data	= null;
		}

		$this->set(compact('arquivo', 'data'));


		if( !empty($this->data) ) {

			$arq	= $this->data['Pagamento']['arquivo'];
			$usuario= $this->Auth->user();
			$qtd	= $this->Pagamento->processarArquivo($arq, $usuario['Usuario']['id']);
			if( $qtd ) {

				$this->Session->setFlash(sprintf(__('Registrados %d pagamentos a partir do arquivo "%s".', true), $qtd, $arq['name']), 'flash_info');
				$this->redirect(array('action'=> 'arquivoretorno'));
			} else {

				$this->Session->setFlash(sprintf(__('Sem pagamentos a registrar no arquivo %s.', true), $arq['name']), 'flash_warning');
			}
		}
	}

/**
 * Action que trata pagamentos direto na administração.
 */
	function administracao() {

		$this->Pagamento->Lancamento->Unidade->contain('Quadra');
		$unidades		= $this->Pagamento->Lancamento->Unidade->findList();

		$this->Pagamento->Lancamento->TipoLancamento->contain();
		$tiposLancamento= $this->Pagamento->Lancamento->TipoLancamento->find('list', array('fields'=> array('cod', 'descricao'), 'order'=> 'cod', 'conditions'=> array('TipoLancamento.cod >='=> 0)));

		$hoje			= date('d/m/Y');
		$formasPagamento= $this->Pagamento->Lancamento->TipoLancamento->find('list');

		$this->set(compact('unidades', 'tiposLancamento', 'hoje', 'formasPagamento'));


		if( !empty($this->data) ) {

			$acrescimo['valor_juros']	= 0.00;
			$acrescimo['valor_multa']	= 0.00;
			$acrescimo['valor_desconto']= 0.00;

			foreach( $this->data['Aux'] as $k=> $valor ) { // XXX: colocar o mesmo campo desconto?

				$valor	= str_replace(',', '.', $valor);
				$acrescimo[$k]	= number_format($valor, 2);
			}
			unset($this->data['Aux']);

			if( $usuario= $this->Auth->user() ) {

				$this->Pagamento->Lancamento->id	= $this->data['Pagamento']['lancamento_id'];
				$this->Pagamento->Lancamento->contain();
				$lancamento	= $this->Pagamento->Lancamento->read();

				$this->data['Pagamento']['valor_documento']	= number_format($lancamento['Lancamento']['valor_documento'], 2, ',', '');

				$this->data['Pagamento']['valor_acrescimo']	= $acrescimo['valor_juros'] + $acrescimo['valor_multa'];
				$this->data['Pagamento']['valor_desconto']	= $acrescimo['valor_desconto'];
				$this->data['Pagamento']['parcela']			= '1'; // TODO: verificar de onde deve vir este campo
				$this->data['Pagamento']['usuario_id']		= $usuario['Usuario']['id'];

				$this->data	= $this->Pagamento->create($this->data);

				if( $this->Pagamento->save($this->data) ) {

					$this->Session->setFlash(sprintf(__('Pagamento registrado no valor de R$ %s.', true), $this->data['Pagamento']['valor_pago']), 'flash_success');
					$this->redirect(array('action'=> 'recibo', $this->data['Pagamento']['lancamento_id']));
				} else {
					// TODO: incluir  mensagem no erro
					// debug($this->Pagamento->invalidFields()); debug($this->data); exit;
					$this->Session->setFlash(__('Oops. Pagamento não registrado. Tente novamente.', true), 'flash_error');
				}
			} else {

				$this->Auth->logout();
			}
		}
	}

/**
 * Action que exibe o texto de um recibo do lançamento correspondente. // TODO: tratar se está pago ou não :-P
 * @param  integer $lancamento_id	o id do lançamento
 */
	function recibo($lancamento_id) {

		$this->Pagamento->Lancamento->contain(array(
			  'Unidade'=> array('Quadra', 'Proprietario')
			, 'Pagamento'
			));
		$this->Pagamento->Lancamento->id	= $lancamento_id;
		$lancamento	= $this->Pagamento->Lancamento->read();

		$this->set(compact('lancamento'));
	}

/**
 * 
 */
	function estorno() {
		
		$mesAnoAtual	= date('m/Y');
		$tiposLancamento= $this->Pagamento->Lancamento->TipoLancamento->find('list');
		unset($tiposLancamento[-1]);
		$unidades		= $this->Pagamento->Lancamento->Unidade->findList();
		$unidades[0]	= __('SELECIONE...', true);
		
		$this->set(compact('unidades', 'tiposLancamento', 'mesAnoAtual'));
		
		if( !empty($this->data) ) {
			
			// valida: 1) mes_ano, 2) existe 1 registro
			$mesAnoPreenchido = preg_match('/\d{2}\/\d{4}/', $this->data['Lancamento']['mes_ano']);
			if( !$mesAnoPreenchido ) {
				$this->Session->setFlash(__('Mês/ano informado incorretamente. Favor corrigir.', true), 'flash_warning');
				return;
			}
			if( !$this->data['Lancamento']['unidade_id'] ) {
				$this->Session->setFlash(__('Unidade não informada. Por favor, selecione-a.', true), 'flash_warning');
				return;
			}
			// procura o pagamento do lancamento desse tipo, unidade e mes_ano
			$this->Pagamento->Lancamento->contain('Pagamento');
			$pgto = $this->Pagamento->Lancamento->find('all', array(
				  'conditions' => array(
					  'Lancamento.mes_ano' => $this->data['Lancamento']['mes_ano']
					, 'Lancamento.tipo_lancamento_id' => $this->data['Lancamento']['tipo_lancamento_id']
					, 'Lancamento.unidade_id' => $this->data['Lancamento']['unidade_id']
				)
			));
			if( sizeof($pgto) == 0 ) {
				$this->Session->setFlash(__('Pagamento não localizado para mês/ano, tipo e unidade informados', true), 'flash_warning');
				return;				
			}
			
			$id = $pgto[0]['Pagamento']['id'];
			if( $this->Pagamento->delete($id) ) {
				$this->Session->setFlash(__('Pagamento estornado com sucesso.', true), 'flash_info');
				return;				
			}
		}
	}

/**
 * Utilizado para o formulário de pagamento direto na administração.
 * Consulta a relação de lançamentos não pagos, devolvendo valores
 * voláteis para o formulário, como os meses e anos, valor do documento,
 * data de vencimento, etc.  Já seleciona o primeiro mes_ano que for
 * retornado.
 */
	function ajaxobterlancamentosaberto() {

		if( !$this->RequestHandler->isAjax() ) {
			$this->redirect(array('controller'=> 'menus', 'action'=> 'index'));
		}
		if( isset($this->data['Lancamento']['unidade_id']) && isset($this->data['Lancamento']['tipo_lancamento_id']) ) {

			$unidade	= $this->data['Lancamento']['unidade_id'];
			$tipo		= $this->data['Lancamento']['tipo_lancamento_id'];
			$lancamentos= $this->Pagamento->Lancamento->obterLancamentosAberto($unidade, null, $tipo);

			$mesesAnos	= Set::combine($lancamentos, '{n}.Lancamento.id', '{n}.Lancamento.mes_ano');

			$this->set(compact('mesesAnos'));
		}
	}

/**
 * Obtém os dados referentes ao lançamento, caso usuário escolha
 * outro entre os lançamentos em aberto.
 */
	function ajaxobterlancamento() {

		$this->autoRender= false;

//		if( !$this->RequestHandler->isAjax() ) {
//			$this->redirect(array('controller'=> 'menus', 'action'=> 'index'));
//		}
//$this->data['Lancamento']['id']	= 474;
		if( isset($this->data['Lancamento']['id']) ) {
// ---
			$this->Pagamento->Lancamento->contain();
			$this->Pagamento->Lancamento->id	= $this->data['Lancamento']['id'];
			$lancamento	= $this->Pagamento->Lancamento->read();

			$hoje		= date('d/m/Y');
			$valores	= $this->__recalcularValorAcrescimo(  $lancamento['Lancamento']['valor_documento']
															, $lancamento['Lancamento']['dta_vencimento']
															, $hoje
															, $lancamento['Lancamento']['tipo_lancamento_id']);

			//$valores['valor_documento']	= $lancamento['Lancamento']['valor_documento'];
			$valores['valor_total']		= $lancamento['Lancamento']['valor_documento']
										+ $valores['valor_multa']
										+ $valores['valor_juros']
										- $valores['valor_desconto'];
//debug($valores); exit;
// ---
			$this->set(compact('valores', 'lancamento', 'hoje'));
			$this->render(null, null, 'form_pagamento');
		}
	}

/**
 * Action para recalcular os valores a partir da data de pagamento.
 *
 * Deve receber os parâmetros:
 * - Lancamento.id
 * - Pagamento.dta_pagamento
 *
 * ...e escrever as seguintes variáveis:
 * - $desconto;
 * - $valorDocumento;
 * - $atraso;
 */
	function ajaxrecalcularpagamento() {

		if( !$this->RequestHandler->isAjax() ) {
			$this->redirect(array('controller'=> 'menus', 'action'=> 'index'));
		}
//$this->data['Lancamento']['id']				= 1;
//$this->data['Pagamento']['dta_pagamento']	= '27/12/2009';
		if( isset($this->data['Lancamento']['id']) && isset($this->data['Pagamento']['dta_pagamento']) ) {
// ---
			$this->Pagamento->Lancamento->id	= $this->data['Lancamento']['id'];
			$this->Pagamento->Lancamento->contain();
			$lancamento	= $this->Pagamento->Lancamento->read();

			$valores	= $this->__recalcularValorAcrescimo($lancamento['Lancamento']['valor_documento']
														, $lancamento['Lancamento']['dta_vencimento']
														, $this->data['Pagamento']['dta_pagamento']
														, $lancamento['Lancamento']['tipo_lancamento_id']);

			$valorDocumento	= $lancamento['Lancamento']['valor_documento'];
			$valores['valor_total']	= $lancamento['Lancamento']['valor_documento']
									+ $valores['valor_multa']
									+ $valores['valor_juros']
									- $valores['valor_desconto'];

			$this->set(compact('valores', 'lancamento'));
		}
	}

/**
 * Recebe o valor total e o valor do desconto dado e permite até um
 * valor determinado de desconto.
 */
	function ajaxincluirdesconto() {

		if( !$this->RequestHandler->isAjax() ) {
			$this->redirect(array('controller'=> 'menus', 'action'=> 'index'));
		}
		if( isset($this->data['Aux']['valor_total']) && isset($this->data['Aux']['valor_desconto']) ) {

			$total		= $this->data['Aux']['valor_total'];
			$desconto	= $this->data['Aux']['valor_desconto'];
			$valor		= $this->Pagamento->incluirDesconto($total, $desconto);

			$this->set(compact('valor'));
		}
	}


/**
 * Não é uma replicação de lógica que está no model, mas um segundo nível
 * de verificação necessário em virtude das questões pessoais do usuário
 * querendo dar descontos a torto e a direito.
 * @see Pagamento::calcularValorAcrescimo();
 */
	function __recalcularValorAcrescimo($valor, $vencimento, $pagamento, $tipo= TIPO_LANCAMENTO_TAXACONDOMINIAL) {

		if( preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $vencimento) ) {

			list($anov, $mesv, $diav)	= explode('-', $vencimento);
		} elseif( preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/', $vencimento) ) {

			list($diav, $mesv, $anov)	= explode('/', $vencimento);
		}

		if( preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $pagamento) ) {

			list($anop, $mesp, $diap)	= explode('-', $pagamento);
		} elseif( preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/', $pagamento) ) {

			list($diap, $mesp, $anop)	= explode('/', $pagamento);
		}
		$vencimento	= sprintf('%s/%s/%s', $diav, $mesv, $anov);
		$pagamento	= sprintf('%s/%s/%s', $diap, $mesp, $anop);

		if( $mesp == $mesv ) {

			$valores	= $this->Pagamento->calcularValorAcrescimo($valor, $vencimento, $pagamento, $tipo);
			$autoAbono	= Configure::read('pagamentos_atraso_autoabono');
			if( ($valores['atraso'] > 0) && $autoAbono ) {
				// autoabono. com atraso dentro do mês dá desconto no mesmo valor do acréscimo.
				$valores['valor_desconto']	= $valores['valor_juros'] + $valores['valor_multa'];
			}
		} else {
			// não está no mesmo mês... deixa a lógica de negócio...
			$valores	= $this->Pagamento->calcularValorAcrescimo($valor, $vencimento, $pagamento, $tipo);
		}
		return $valores;
	}

}

