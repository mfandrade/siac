<?php
class LancamentosController extends AppController {
	var $name		= 'Lancamentos';
	var $components	= array('BoletoPdf');
	var $uses		= array('Lancamento', 'Boleto');
	var $helpers	= array('Javascript', 'Ajax', 'Formatar');

/**
 * Action.
 */
	function index() {
		$this->redirect(array('controller'=> 'menus', 'action'=> 'lancamentos'));
	}

/**
 * Efetua o lançamentos de taxa condominial para todas as unidades.
 */
	function taxacondominial() {

		$diretorio		= Configure::read('boletos_arquivo_diretoriogravacao');
		$dia			= Configure::read('lancamentos_taxacondominial_vencimento_dia');
		$antecedencia	= Configure::read('lancamentos_taxacondominial_vencimento_antecedencia');
		$qtdMeses		= Configure::read('lancamentos_qtdmeses_listagem');
		$mesAno			= $this->Lancamento->obterMesAnoDefaultLancamento($dia, $antecedencia);
		$mesesAnos		= $this->Lancamento->obterMesesAnosLancamento($dia, $antecedencia, $qtdMeses);

		$valorDocumento	= Configure::read('lancamentos_taxacondominial_valor');

		$this->Lancamento->InstrucaoBoleto->contain();
		$instrucoes		= $this->Lancamento->InstrucaoBoleto->find('list', array(
			  'conditions'	=> array('InstrucaoBoleto.tipo_lancamento_id'=> array(TIPO_LANCAMENTO_QUALQUER, TIPO_LANCAMENTO_TAXACONDOMINIAL))
			, 'order'		=> 'InstrucaoBoleto.id'
		));
		$this->set(compact('diretorio', 'mesesAnos', 'qtdMeses', 'mesAno', 'valorDocumento', 'instrucoes'));


		if( !empty($this->data) ) {

			$this->data['Lancamento']['tipo_lancamento_id']	= TIPO_LANCAMENTO_TAXACONDOMINIAL;
			$this->data['Lancamento']['valor_documento']	= $valorDocumento;
			$usuario	= $this->Auth->user();
			$this->data['Lancamento']['usuario_id']			= $usuario['Usuario']['id'];
			if( $this->Lancamento->efetuarLancamentos($this->data) ) {

				// gera mensagem de status, salva os dados na sessão e redireciona para os boletos
				$msg	= 'Taxa condominial do mês ' . $this->data['Lancamento']['mes_ano'] . ' lançada para todas as unidades.';
				$this->Session->write('LANCAMENTOS.DADOS', $this->data);
				$this->redirect(array('action'=> 'gerarboletos'/*, urlencode($msg)*/));
			} else {
				$this->Session->setFlash(__('Algum problema ocorreu. Lançamentos não efetuados, tente novamente.', true), 'flash_error');
			}
		}
	}

/**
 * Action cuja view é responsável pela geração de boletos via ajax.
 */
	function gerarboletos($msg= null) {

		if( !$this->Session->check('LANCAMENTOS.DADOS') ) {
			$this->redirect(array('controller'=> 'menus', 'action'=> 'index'));
		}
		$msg	= urldecode($msg);
		$this->set(compact('msg'));
	}

/**
 * Action responsável diretamente pela geração do boleto.  Só deve
 * ser chamada via requisição Ajax.  Lê as chaves de sessão
 * LANCAMENTOS.DADOS (vinda de @see taxacondominial e etc) e escreve
 * as LANCAMENTOS.PAGINAS e LANCAMENTOS.PAGINA, sobre o andamento do
 * boleto pdf (para @see ajaxatualizarstatusboletos).
 *
 * ['Lancamento']['tipo_lancamento_id']	- default, 0
 * ['Lancamento']['mes_ano']			- default, atual
 * ['Lancamento']['unidade_id']			- default, null
 * ['Lancamento']['valor_documento']	- default, chave ... de config
 * ['Lancamento']['instrucao_boleto_id']
 */
	function ajaxgerarboletos() {

		if( !$this->RequestHandler->isAjax() || !$this->Session->check('LANCAMENTOS.DADOS') ) {
			$this->redirect(array('controller'=> 'menus', 'action'=> 'index'));
		}
		$dados		= $this->Session->read('LANCAMENTOS.DADOS');

/* DEBUG... 
$dados['Lancamento']['tipo_lancamento_id']	= 1;
$dados['Lancamento']['mes_ano']				= '07/2010';
$dados['Lancamento']['unidade_id']			= 1;
$dados['Lancamento']['valor_documento']		= '280.00';
$dados['Lancamento']['instrucao_boleto_id']	= 2;
// */
		switch( $dados['Lancamento']['tipo_lancamento_id'] ) {
			default:
			case TIPO_LANCAMENTO_TAXACONDOMINIAL:
					$dia	= Configure::read('lancamentos_taxacondominial_vencimento_dia');
					$tipoArq= 'taxacondominial';
					break;
			case TIPO_LANCAMENTO_TAXAEXTRA:
					$dia	= Configure::read('lancamentos_taxaextra_vencimento_dia');
					$tipoArq= 'taxaextra';
					break;
			case TIPO_LANCAMENTO_MULTAINFRACAO:
					$dia	= Configure::read('lancamentos_multainfracao_vencimento_dia');
					$tipoArq= 'multainfracao';
					break;
			case TIPO_LANCAMENTO_ACORDO:
					$dia	= Configure::read('lancamentos_acordo_vencimento_dia');
					$tipoArq= 'acordo';
					break;
		}
		list($mes, $ano)	= explode('/', $dados['Lancamento']['mes_ano']);
		$vencimento			= sprintf('%s/%s/%s', $dia, $mes, $ano);

		$diretorio		= Configure::read('boletos_arquivo_diretoriogravacao');
		$nomeArquivo	= Configure::read('boletos_arquivo_nomearquivo');

		$s	= array('{TIPO}', '{ANOMES}');	// XXX: chaves: {TIPO}, {ANOMES}... que mais?
		$r	= array($tipoArq, $ano . $mes);

		$this->Lancamento->Unidade->contain(array('Proprietario.nome', 'Proprietario.endereco', 'Proprietario.cep', 'Proprietario.bairro', 'Proprietario.cidade', 'Proprietario.uf', 'Quadra.abbr'));
		if( !empty($dados['Lancamento']['unidade_id']) ) {

			$this->Lancamento->Unidade->id	= $dados['Lancamento']['unidade_id'];
			$unidade	= $this->Lancamento->Unidade->read();
			$unidades[]	= $unidade;

			$quadra		= $unidade['Quadra']['abbr'];
			$lote		= $unidade['Unidade']['lote'];
			$sufixo		= sprintf('-%sl%02d', strtolower($quadra), $lote);

			$nomeArquivo= str_replace('.pdf', $sufixo.'.pdf', $nomeArquivo);
		} else {

			$unidades			= $this->Lancamento->Unidade->find('all', array('order'=> array('quadra_id', 'lote')));
		}
		$nomeArquivo	= str_replace($s, $r, $nomeArquivo);
		$filepath		= $diretorio . DS . $nomeArquivo;

		if( !empty($dados['Lancamento']['instrucao_boleto_id']) ) {

			$this->Lancamento->InstrucaoBoleto->id	= $dados['Lancamento']['instrucao_boleto_id'];
			$instrucoes		= $this->Lancamento->InstrucaoBoleto->read();
			$instrucoes		= $instrucoes['InstrucaoBoleto']['texto'];
		} elseif( !empty($dados['Lancamento']['instrucoes']) ) {

			$instrucoes		= $dados['Lancamento']['instrucoes'];
		}

		$valor		= $dados['Lancamento']['valor_documento'];
		$tipo		= $dados['Lancamento']['tipo_lancamento_id'];

		$this->__gerarBoleto($filepath, $unidades, $vencimento, $valor, $instrucoes, $tipo);
		$this->set(compact('nomeArquivo', 'diretorio'));
	}

/**
 * Action para a view de regeração de boletos.
 * @return void
 */
	function regerarboletos() {

		$dia			= Configure::read('lancamentos_taxacondominial_vencimento_dia');
		$antecedencia	= Configure::read('lancamentos_taxacondominial_vencimento_antecedencia');
		$mesAno			= $this->Lancamento->obterMesAnoDefaultLancamento($dia, $antecedencia);
		$mesesAnos		= $this->Lancamento->obterMesesAnosLancados(TIPO_LANCAMENTO_TAXACONDOMINIAL);
		$unidades		= $this->Lancamento->Unidade->findList();

		$this->Lancamento->TipoLancamento->contain();
		$tiposLancamento= $this->Lancamento->TipoLancamento->find('list', array('fields'=> array('cod', 'descricao'), 'conditions'=> array('TipoLancamento.cod >='=> 0)));
		$this->Lancamento->InstrucaoBoleto->contain();
		$instrucoes		= $this->Lancamento->InstrucaoBoleto->find('list');

		$this->set(compact('mesAno', 'mesesAnos', 'unidades', 'tiposLancamento', 'instrucoes'));


		if( !empty($this->data) ) {

			if( empty($this->data['Lancamento']['mes_ano']) ) {

				$this->Session->setFlash(__('Falha na solicitação. Mês/ano não especificado.', true), 'flash_error');
				$this->redirect(array('action'=> 'regerarboletos'));
			}

			if( !empty($this->data['Lancamento']['unidade_id']) ) {

				$lancamento	= $this->Lancamento->find('first', array('conditions'=> array(
					  'Lancamento.unidade_id'			=> $this->data['Lancamento']['unidade_id']
					, 'Lancamento.mes_ano'				=> $this->data['Lancamento']['mes_ano']
					, 'Lancamento.tipo_lancamento_id'	=> $this->data['Lancamento']['tipo_lancamento_id']
				)));
				$lancamento['Lancamento']['instrucao_boleto_id'] = $this->data['Lancamento']['instrucao_boleto_id'];
				
				$this->Session->write('LANCAMENTOS.DADOS', $lancamento);
			} else {
				
				// recupera um lancamento do tipo dado do mes_ano dado
				$this->Lancamento->contain();
				$lancamentoModelo = $this->Lancamento->find('first', array('conditions' => array(
					  'Lancamento.tipo_lancamento_id' => $this->data['Lancamento']['tipo_lancamento_id']
					, 'Lancamento.mes_ano' => $this->data['Lancamento']['mes_ano']
				)));
				$sessao['Lancamento']['mes_ano']			= $this->data['Lancamento']['mes_ano'];
				$sessao['Lancamento']['tipo_lancamento_id']	= $this->data['Lancamento']['tipo_lancamento_id'];
				$sessao['Lancamento']['valor_documento']	= $lancamentoModelo['Lancamento']['valor_documento'];
				$sessao['Lancamento']['instrucao_boleto_id']= $this->data['Lancamento']['instrucao_boleto_id'];
				$this->Session->write('LANCAMENTOS.DADOS', $sessao);
			}
			$this->redirect(array('action'=> 'gerarboletos'/*, urlencode($msg)*/));
		}
	}

/**
 * Efetua o cadastro e os lançamentos de taxa extra para todas as unidades.
 */
	function taxaextra() {

		$dia			= Configure::read('lancamentos_taxacondominial_vencimento_dia');
		$antecedencia	= Configure::read('lancamentos_taxacondominial_vencimento_antecedencia');
		$mesAno			= $this->Lancamento->obterMesAnoDefaultLancamento($dia, $antecedencia);
		$hoje			= date('d/m/Y');

		$this->set(compact('mesAno', 'hoje'));


		if( !empty($this->data) ) {

			if( !is_numeric($this->data['Taxa']['qtd_parcelas']) ) {
				$this->data['Taxa']['qtd_parcelas']	= 1;
			}
			$valorTotal		= $this->data['Taxa']['valor_total'];
			$qtdParcelas	= $this->data['Taxa']['qtd_parcelas'];
			$valorDocumento	= $valorTotal / $qtdParcelas;
			$valorDocumento	= sprintf('%.2f', $valorDocumento);
			$usuario		= $this->Auth->user();

			$taxa['Taxa']	= $this->data['Taxa'];
			$taxa			= $this->Lancamento->Taxa->create($taxa);
			if( $this->Lancamento->Taxa->save($taxa) ) {

				unset($this->data['Taxa']);
				$this->data['Lancamento']['taxa_id']			= $this->Lancamento->Taxa->id;
				$this->data['Lancamento']['tipo_lancamento_id']	= TIPO_LANCAMENTO_TAXAEXTRA;
				$this->data['Lancamento']['usuario_id']			= $usuario['Usuario']['id'];
				$this->data['Lancamento']['valor_documento']	= $valorDocumento;
				$this->data['Lancamento']['instrucao_boleto_id']= null;

				if( $this->Lancamento->efetuarLancamentos($this->data, $qtdParcelas) ) {
					// gera mensagem de status, salva os dados na sessão e redireciona para os boletos
					$msg	= 'Taxa extra "' . $this->data['Lancamento']['ref'] . '" para o mês ' . $this->data['Lancamento']['mes_ano'] . ' lançada para todas as unidades.';
					$this->Session->write('LANCAMENTOS.DADOS', $this->data);
					$this->redirect(array('action'=> 'gerarboletos'/*, urlencode($msg)*/));

				} else {
					$this->Session->setFlash(__('Oops. Lançamentos não efetuados. Corrija os erros e tente novamente.', true), 'flash_error');
				}
			} else {
				// para exibir corretamente na validação
				$this->Session->setFlash(__('Oops. Taxa não cadastrada. Corrija os erros e tente novamente.', true), 'flash_error');
				$this->data['Taxa']	= $taxa['Taxa'];
			}
		}
	}

/**
 * Action privada que encapsula toda a lógica para geração de boleto.
 * @param  string  $filepath   o caminho completo de onde o arquivo será salvo
 * @param  array   $unidades   conjunto de unidades para as quais gerar
 * @param  string  $vencimento data de vencimento no formato humano (TODO: retirar, jogar para dentro das chaves do boleto)
 * @param  float   $valor      valor do documento no formato humano
 * @param  string  $instrucoes texto a constar das instruções do boleto (TODO: retirar, jogar para dentro das chaves do boleto)
 * @param  integer $tipo       tipo de lançamento para o qual gerar o boleto (TODO: retirar, obter a partir do número)
 * @return void
 */
	function __gerarBoleto($filepath, $unidades, $vencimento, $valor, $instrucoes, $tipo= TIPO_LANCAMENTO_TAXACONDOMINIAL, $boleto= array()) {

		$boleto['data_vencimento']		= $vencimento;
		$boleto['agencia_cod_cedente']	= Configure::read('boletos_dados_agencia_cod_cedente');
		$boleto['cedente']				= Configure::read('boletos_dados_cedente');
		$boleto['cpf_cnpj_cedente']		= Configure::read('boletos_dados_cpfcnpj_cedente');

		$boleto['instrucoes']			= $instrucoes;
		$boleto['valor_documento']		= number_format($valor, 2, ',', '');
		$boleto['data_vencimento']		= $vencimento;
		list($dia, $mes, $ano)			= explode('/', $vencimento);

		$i= 0;
		foreach( $unidades as $unidade ) {

			$quadra	= str_pad($unidade['Unidade']['quadra_id'], 2, '0', STR_PAD_LEFT);
			$lote	= str_pad($unidade['Unidade']['lote'], 2, '0', STR_PAD_LEFT);

			$boleto['numero_documento']	= $this->Boleto->gerarNumeroDocumento($quadra, $lote, $mes, $ano, $tipo);
			$boleto['nosso_numero']		= $this->Boleto->gerarNossoNumero($boleto['numero_documento'], Configure::read('boletos_dados_agencia_cod_cedente'), $vencimento);

			$boleto['sacado']			= $unidade['Proprietario']['nome'];
			$boleto['sacado_endereco']	= $unidade['Proprietario']['endereco'];
			$boleto['sacado_cep']		= $unidade['Proprietario']['cep'];
			$boleto['sacado_bairro']	= $unidade['Proprietario']['bairro'];
			$boleto['sacado_cidade']	= $unidade['Proprietario']['cidade'];
			$boleto['sacado_uf']		= $unidade['Proprietario']['uf'];
			if( empty($boleto['sacado_endereco']) ) {

				$boleto['sacado_endereco']	= 'ROD. ARTUR BERNARDES, 1650'; // TODO, refatorar para uso de constantes
				$boleto['sacado_cep']		= '66825000';
				$boleto['sacado_bairro']	= 'PRATINHA I';
				$boleto['sacado_cidade']	= 'BELÉM';
				$boleto['sacado_uf']		= 'PA';
			}

			$boleto['sacado_complemento']		=  $unidade['Quadra']['abbr'].'L'.$lote;

			$this->BoletoPdf->novaPagina('HSBC', $boleto);
		}

		return $this->BoletoPdf->saveFile($filepath);
	}
}
