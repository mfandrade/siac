<?php
class ArquivoRetorno extends AppModel {
	var $name		= 'ArquivoRetorno';
	var $hasMany	= 'Pagamento';
	var $validate	= array(
		  'arquivo'		=> array('rule'=> '/.TXT$/i', 'required'=> true, 'message'=> 'Arquivo não tem a extensão .TXT')
		, 'dta_gravacao'=> array(
			  'data'	=> array('rule'=> 'date', 'required'=> true, 'message'=> 'Data de gravação no arquivo está corrompida ou inexistente')
			, 'unica'	=> array('rule'=> array('isUnique2', array('dta_gravacao', 'processado')), 'message'=> 'Já fora processado o arquivo de retorno para esta data')
		)
		, 'processado'	=> array('rule'=> 'boolean', 'required'=> true, 'message'=> 'Obrigatória a indicação se o arquivo foi processado')
	);

/**
 * Versão do método de validação "isUnique" que considera o nome do campo
 * informado como flag para permissão de duplicidade.
 * @param  mixed   $value  o valor do registro em questão.
 * @param  array   $params um array contendo o campo a comparar e o flag
 * @return boolean true se $value já existir e valor do flag for false, ou se não existir e o flag for true.
 */
	function isUnique2($value, $params) {

		$field	= $params[0];
		$flag	= $params[1];
		$this->data	= $this->create($this->data);

		if( !$this->data[$this->alias][$flag] ) return true;
		else {

			$fieldVal	= $this->data[$this->alias][$field];

			$existingFieldsCount	= $this->find('count', array(
				  'conditions'=> array(
					  'and'=> array(
						  $this->alias.'.'.$flag=> true
						, $this->alias.'.'.$field=> $fieldVal
					)
				)
				, 'recursive'=> -1
			));
			return $existingFieldsCount == 0;
		}
	}

/**
 * Obtém apenas a data de gravação a partir do conteúdo do arquivo;
 * @param  $file  ou o campo array arquivo ou apenas a chave com o nome do caminho
 * @return a data de gravação do arquivo no formato de banco
 */
	function obterDataGravacao($file) {

		if( is_array($file) ) $filepath= $file['tmp_name'];
		elseif( is_string($file) && file_exists($file) ) $filepath= $file;
		else return null;

		$res	= fopen($filepath, 'r');
		$header	= fgets($res);
		fclose($res);

		$diaMesAno	= substr($header, 94, 6);
		$dia		= substr($diaMesAno, 0, 2);
		$mes		= substr($diaMesAno, 2, 2);
		$ano		= substr($diaMesAno, 4);
		$dta_gravacao	= '20'.$ano.'-'.$mes.'-'.$dia;

		return $dta_gravacao;
	}

/**
 * Processa o arquivo de retorno, verificando se 'e valido, se jah
 * foi processado, se nao estah corrompido, etc.  Retorna um array
 * de pagamento no formato do Cake se tudo estiver okay.
 *
 * @param  mixed   $arq			caminho do arquivo ou dados de arquivo "uploaded"
 * @param  integer $usuario_id	id do usuário autenticado
 * @param  integer $arquivo_id	id do arquivo de retorno, se já estiver cadastrado
 * @param  boolean $force		se deve forçar a operação mesmo se ocorrer algum problema em algum registro
 * @return array 	dados do model pagamento no formato do Cake ou null em caso de erro
 */
	function extrairPagamentos400($arq, $usuario_id, $arquivo_id= null, $force= false) {

		if( is_array($arq) ) {
			$filename		= strtoupper($arq['name']);
			$filepath		= $arq['tmp_name'];

		} elseif( is_string($arq) && file_exists($arq) ) {
			$filepath		= $arq;
			$filename		= strtoupper(basename($arq));
		} else return false;

		if( !file_exists($filepath) ) return null;

		$this->currentFile	= $filename;

		$dados	= file($filepath);
		$header	= $dados[0];
		$trailer= $dados[sizeof($dados)-1];

		$validacao	= $this->__validarHeaderArquivo($header);
		if( is_string($validacao) ) {
			$params	= array('secao'=> 'HEADER', 'msg'=> $validacao);
			$this->cakeError('erroArquivoRetorno', $params);
			return null;
		}
		$validacao	= $this->__validarTrailerArquivo($trailer);
		if( is_string($validacao) ) {
			$params	= array('secao'=> 'TRAILER', 'msg'=> $validacao);
			$this->cakeError('erroArquivoRetorno', $params);
			return null;
		}
		$validacao	= null;

		$dados	= $this->__lerDadosArquivo($dados, $usuario_id, $arquivo_id, $force);
		return $dados;
	}

/**
 * Método __validarHeaderArquivo.
 * @param  string $linha  a linha do arquivo
 * @return array dados do header do arquivo a partir da linha.
 */
	function __validarHeaderArquivo($linha) {

		$gabarito= array(			// array(tamanho, valor)
			  'cod_registro'		=> array(1, '0')
			, 'cod_retorno'			=> array(1, '2')
			, 'literal_retorno'		=> array(7, 'RETORNO')
			, 'cod_servico'			=> array(2, '01')
			, 'literal_servico'		=> array(15,'COBRANCA CNR   ')
			, 'cod_agencia_cedente'	=> array(5, null)
			, 'constante'			=> array(2, '00')
			, 'conta_corrente'		=> array(11, null)
			, 'tipo_retorno'		=> array(1, ' ')
			, 'brancos1'			=> array(1, ' ')
			, 'nome_cedente'		=> array(30, null)
			, 'cod_banco'			=> array(3, Configure::read('boletos_dados_banco_codigo'))
			, 'nome_banco'			=> array(15,'HSBC           ')
			, 'data_gravacao'		=> array(6, null)
			, 'densidade'			=> array(5, array('01600', '06250'))
			, 'literal_densidade'	=> array(3, 'BPI')
			, 'cod_cedente'			=> array(10, null)
			, 'nome_agencia'		=> array(20, null)
			, 'cod_formulario'		=> array(4, null)
			, 'brancos2'			=> array(246, null)
			, 'volser'				=> array(6, null)
			, 'seq'					=> array(6, '000001')
		);
		return $this->__validarLinhaArquivo($linha, $gabarito);
	}

/**
 * Método __validarTrailerArquivo.
 * @param  string $linha  a linha do arquivo
 * @return array dados do trailer do arquivo a partir da linha.
 */
	function __validarTrailerArquivo($linha) {

		$gabarito= array(		// array(tamanho, valor)
			  'cod_registro'	=> array(1, '9')
			, 'cod_retorno'		=> array(1, '2')
			, 'cod_servico'		=> array(2, '01')
			, 'cod_banco'		=> array(3, Configure::read('boletos_dados_banco_codigo'))
			, 'brancos1'		=> array(387, null)
			, 'seq'				=> array(6, null)
		);
		return $this->__validarLinhaArquivo($linha, $gabarito);
	}

/**
 * Método __validarLinhaArquivo.
 * @param  string $linha    a linha do arquivo
 * @param  array  $gabarito array com definição de campos e validação da linha
 * @return array dados a partir da linha.
 */
	function __validarLinhaArquivo($linha, $gabarito) {

		$tipo	= substr($linha, 0, 1);
		if( $tipo == '0' ) {
			$secao	= 'HEADER';
		} elseif( $tipo == '9' ) {
			$secao	= 'TRAILER';
		} elseif( $tipo == '1' ) {
			$secao	= 'DETAIL';
		}
		$cursor	= 0;
		foreach($gabarito as $campo=> $array) {
			$tamanho	= $array[0];
			$comparacao	= $array[1];

			$data[$campo]	= substr($linha, $cursor, $tamanho);
			if( is_array($comparacao) ) {
				if( !in_array($data[$campo], $comparacao) ) {
					$msg	= sprint('ERRO %s: %s (linha: %s)', $secao, $campo, $cursor+1);
					return	$msg;
				}
			} elseif( is_string($comparacao) ) {
				if( $data[$campo] !== $comparacao ) {
					$msg	= sprint('ERRO %s: %s (linha: %s)', $secao, $campo, $cursor+1);
					return	$msg;
				}
			}
			$cursor	+= $tamanho;
		}
		return $data;
	}

/**
 * Método __lerDadosArquivo.
 * @param  string  $dados        a linha do arquivo
 * @param  integer $usuario_id   id do usuário autenticado
 * @param  integer $arquivo_id   id do arquivo cadastrado
 * @param  boolean $force        se deve continuar processando mesmo no caso de um problema com os dados do arquivo
 * @return array   dados a partir da linha.
 */
	function __lerDadosArquivo($dados, $usuario_id, $arquivo_id, $force= false) {

		$gabarito= array(			// array(tamanho, valor)
			  'cod_registro'		=> array(1, '1')
			, 'cod_inscricao'		=> array(2, '99')
			, 'cod_cedente'			=> array(14, null)
			, 'cod_agencia_cedente'	=> array(5, null)
			, 'subconta'			=> array(2, '00')
			, 'conta_corrente'		=> array(11, null)
			, 'brancos1'			=> array(2, null)
			, 'cod_documento1'		=> array(16, null)
			, 'brancos2'			=> array(1, null)
			, 'cod_postagem'		=> array(1, array(' ', '1', '2')) // erro no documento? ou no arquivo??
			, 'brancos3'			=> array(7, null)
			, 'cod_documento2'		=> array(16, null)
			, 'brancos4'			=> array(4, null)
			, 'data_credito'		=> array(6, null)
			, 'moeda'				=> array(1, array(MOEDA_REAL, MOEDA_VARIAVEL))
			, 'brancos5'			=> array(18, null)
			, 'carteira'			=> array(1, '1')
			, 'cod_ocorrencia'		=> array(2, array(OCORRENCIA_RETORNO_LIQUIDACAO, OCORRENCIA_EMISSAO_CONFIRMADA, OCORRENCIA_PARCELA_REJEITADA))
			, 'data_ocorrencia'		=> array(6, null)
			, 'seu_numero'			=> array(6, null)
			, 'motivo_ocorrencia'	=> array(9, null)
			, 'brancos6'			=> array(15, null)
			, 'data_vencimento'		=> array(6, null)
			, 'valor_titulo'		=> array(13, null)
			, 'banco_cobrador'		=> array(3, null)
			, 'agencia_cobradora'	=> array(5, null)
			, 'especie'				=> array(2, '99')
			, 'valor_iof'			=> array(11, null)
			, 'brancos7'			=> array(54, null)
			, 'valor_desconto'		=> array(13, null)
			, 'valor_pago'			=> array(13, null)
			, 'valor_juros'			=> array(13, null)
			, 'constante'			=> array(1, '0')
			, 'qtd_moeda'			=> array(13, null)
			, 'cotacao_moeda'		=> array(15, null)
			, 'status_parcela'		=> array(1, array(STATUS_PARCELA_CORRETA, STATUS_PARCELA_REGULARIZADA, STATUS_PARCELA_PENDENTE))
			, 'id_lancamento_cc'	=> array(6, null)
			, 'brancos8'			=> array(26, null)
			, 'tipo_liquidacao'		=> array(1, array(TIPO_LIQUIDACAO_CHEQUE, TIPO_LIQUIDACAO_DINHEIRO, TIPO_LIQUIDACAO_COMPENSACAO))
			, 'origem_tarifa'		=> array(1, array('1', '2', '3'))
			, 'brancos9'			=> array(51, null)
			, 'seq'					=> array(6, null)
		);
$this->log('-[ '.$arquivo_id.' ]----------------------------------------------------------------', 'info');
		$data= array();
		for( $i= 1; $i < sizeof($dados)-1; $i++ ) {

			$linha		= $dados[$i];
			$validacao	= $this->__validarLinhaArquivo($linha, $gabarito);
			if( is_string($validacao) ) {

				if( $force ) continue; // ignora linhas problemáticas
				// aborta ao encontrar linhas com problema
				$params['secao']	= 'DETAIL';
				$params['msg']		= sprintf('%s (linha:%s)', $validacao, $i);
				$this->cakeError('erroArquivoRetorno', $params);
				return null;
			}
			$campos		= $validacao;
			$validacao	= null;

			if( $campos['status_parcela'] != STATUS_PARCELA_CORRETA ) {
$this->log('status_parcela = '.$campos['status_parcela'].'... registro ignorado', 'debug');
				continue;
			}

			if( $campos['cod_ocorrencia'] != OCORRENCIA_RETORNO_LIQUIDACAO ) {
				$motivos	= $this->__erroMotivoOcorrencia($campos['motivo_ocorrencia']);
				if( !empty($motivos) ) {
					$motivo	= implode(', ', $motivos);
					$motivo	= '('.$motivo.')';
				}
$this->log('cod_ocorrencia = '.$campos['cod_ocorrencia'].'... registro ignorado.', 'debug');
$this->log('^---motivo: '.$motivo, 'info');
				continue;
			}
			// trata alguns dados
			$dta_pagamento	= $this->__converterData($campos['data_ocorrencia']);
			$dta_vencimento	= $this->__converterData($campos['data_vencimento']);

			$valor_desconto	= $this->__converterValor($campos['valor_desconto']);
			$valor_acrescimo= $this->__converterValor($campos['valor_juros']);
			$valor_pago		= $this->__converterValor($campos['valor_pago']);
			$valor_documento= $this->__converterValor($campos['valor_titulo']);

			$quadra	= substr($campos['cod_documento2'], 5, 2);
			$lote	= substr($campos['cod_documento2'], 7, 2);
			$mm		= substr($campos['cod_documento2'], 9, 2);
			$aa		= substr($campos['cod_documento2'], 11, 2);
			$mes_ano= $mm.'/20'.$aa;

			$this->Pagamento->Lancamento->Unidade->contain();
			$unidade= $this->Pagamento->Lancamento->Unidade->find('first', array(
				  'conditions'=> array(
					  'and'=> array(
						  'Unidade.quadra_id'=> $quadra
						, 'Unidade.lote'=> $lote
					)
				)
			));
// ----------------------------------------------------------------------
			if( $quadra == 20 ) { // considera unidade 473
				$unidade['Unidade']['id']		= 473;
				$unidade['Unidade']['quadra_id']= 20;
				$unidade['Unidade']['lote']		= 'XX';
			}
// ----------------------------------------------------------------------
$this->log(sprintf('%s:%-3d mes_ano:%s, unidade_id:%3d (Q%02dL%02d), valor_documento:%4.2f', $this->currentFile, $i, $mes_ano, $unidade['Unidade']['id'], $unidade['Unidade']['quadra_id'], $unidade['Unidade']['lote'], $valor_documento), 'info');
			$this->Pagamento->Lancamento->contain('Pagamento');
			$lancamentos= $this->Pagamento->Lancamento->find('all', array('conditions'=> array(
				  'Lancamento.mes_ano'				=> $mes_ano
				, 'Lancamento.unidade_id'			=> $unidade['Unidade']['id']
				, 'Lancamento.valor_documento'		=> $valor_documento
			)));
			$tipo	= TIPO_LANCAMENTO_TAXACONDOMINIAL;
			if( sizeof($lancamentos) > 1 ) { // se não bastou para identificar, utiliza também o tipo

				$this->Pagamento->Lancamento->contain('Pagamento');
				$tipo		= $this->Pagamento->Lancamento->TipoLancamento->findByNossoNumero($campos['cod_documento2']);
				$lancamentos= $this->Pagamento->Lancamento->find('all', array('conditions'=> array(
					  'Lancamento.mes_ano'				=> $mes_ano
					, 'Lancamento.tipo_lancamento_id'	=> $tipo
					, 'Lancamento.unidade_id'			=> $unidade['Unidade']['id']
					, 'Lancamento.valor_documento'		=> $valor_documento
				)));
$this->log('achou mais de um lançamento... diferenciando pelo tipo:'.$tipo, 'info');
			} elseif( sizeof($lancamentos) == 0 ) {

				// se não achou para mes, unidade e valor, verifica se o valor estah errado
				$lancamentos= $this->Pagamento->Lancamento->find('all', array('conditions'=> array(
					  'Lancamento.mes_ano'			=> $mes_ano
					, 'Lancamento.unidade_id'		=> $unidade['Unidade']['id']
					, 'Lancamento.valor_documento'	=> Configure::read('lancamentos_taxacondominial_valor')
				)));
				if( sizeof($lancamentos) > 0 ) {
					// okay, indicio razoavel, mas insuficiente. verifica se é o caso de alteração no valor liquido e desconto
					foreach( $lancamentos as $lancamento ) {
						// confirma se é taxacondominial
						$desconto		= Configure::read('lancamentos_taxacondominial_valor_descontopadrao');
						$valor_efetivo	= number_format($lancamento['Lancamento']['valor_documento'] - $desconto, 2);
						if( ($valor_efetivo == $valor_pago) && ($lancamento['Lancamento']['tipo_lancamento_id'] == TIPO_LANCAMENTO_TAXACONDOMINIAL) ) {
							// o zémané do caixa ignorou o valor do titulo...
							$id	= $lancamento['Lancamento']['id'];
$this->log('taxa condominial com valor diferente mas mesmo valor líquido. atualizando lançamento '.$id.' para '.$valor_efetivo.'... ', 'info');
							$this->Pagamento->Lancamento->id	= $id;
							$ok	= $this->Pagamento->Lancamento->updateAll(array( // XXX: problema no driver sqlite
								  'Lancamento.valor_documento'=> $valor_efetivo
							), 'Lancamento.id='.$id);
						}
					}
				} else {
					// aparentemente alguém não fez o dever de casa... vamos considerar acordo
					// caso excepcional... não deveria acontecer. faz o lançamento como acordo
					$nlancamento['mes_ano']			= $mes_ano;
					$nlancamento['unidade_id']		= $unidade['Unidade']['id'];
					$nlancamento['dta_vencimento']	= $dta_vencimento;
					$nlancamento['valor_documento']	= $valor_documento;
					$nlancamento['usuario_id']		= $usuario_id;
					$nlancamento['tipo_lancamento_id']	= TIPO_LANCAMENTO_ACORDO;
					$lancamento['Lancamento']	= $nlancamento;
$this->log('não achou nenhum lançamento... criando um do tipo ACORDO... ', 'info');

					if( $this->Pagamento->Lancamento->save($lancamento) ) {

						$lancamento		= $this->Pagamento->Lancamento->read();
						$lancamentos[0]	= $lancamento;
					}
				}
			}
			$registro	= $lancamentos[0];
			$registro['Pagamento']['lancamento_id']		= $registro['Lancamento']['id'];
			$registro['Pagamento']['dta_pagamento']		= $dta_pagamento;
			$registro['Pagamento']['valor_documento']	= $valor_documento;
			$registro['Pagamento']['valor_desconto']	= number_format($valor_desconto, 2, '.', '');
			$registro['Pagamento']['valor_acrescimo']	= number_format($valor_acrescimo, 2, '.', '');
			$registro['Pagamento']['valor_pago']		= number_format($valor_pago, 2, '.', '');
			$registro['Pagamento']['arquivo_retorno_id']= $arquivo_id;
			$registro['Pagamento']['linha_arquivo']		= $i;
			$registro['Pagamento']['usuario_id']		= $usuario_id;
			$registro['Pagamento']['parcela']			= (int) substr($campos['seu_numero'], 0, 3);
			if( empty($registro['Pagamento']['parcela']) ) {

				$registro['Pagamento']['parcela']		= 1;
			}

			unset($registro['Lancamento']);

			if( $campos['tipo_liquidacao'] == TIPO_LIQUIDACAO_CHEQUE ) {
				// TODO: considerar o status no caso de cheques devolvidos
				$dia	= substr($campos['data_credito'], 0, 2);
				$mes	= substr($campos['data_credito'], 2, 2);
				$ano	= substr($campos['data_credito'], 4);
				$registro['Pagamento']['cheque_dia']		= sprintf('%s-%s-%s', $ano, $mes, $dia);
				$registro['Pagamento']['cheque_banco_id']	= null; // como achar o banco?
				$registro['Pagamento']['cheque_info']		= null;
				$registro['Pagamento']['cheque']			= $registro['Pagamento']['valor_pago'];
				$registro['Pagamento']['compensacao']		= false;

			} elseif( $campos['tipo_liquidacao'] == TIPO_LIQUIDACAO_DINHEIRO ) { // XXX: dinheiro
				$registro['Pagamento']['compensacao']		= false;

			} elseif( $campos['tipo_liquidacao'] == TIPO_LIQUIDACAO_COMPENSACAO ) {
				$registro['Pagamento']['compensacao']		= true;
			}
			$data[]	= $registro;
		}
$this->log('fim da leitura do arquivo...', 'info');
		return $data;
	}

/**
 * Devolve um array contendo a descrição dos códigos de ocorrência.
 * @param  string $motivo  o campo "motivo_ocorrencia" do detalhe do registro
 * @return array  um conjunto (até três) descrições de ocorrência correspondentes
 */
	function __erroMotivoOcorrencia($motivo) {
		$motivo	= str_pad($motivo, 9, '0', STR_PAD_LEFT);
		$cod1	= substr($motivo, 0, 3);
		$cod2	= substr($motivo, 3, 3);
		$cod3	= substr($motivo, 6);
		$codigos= array(
			  '001'	=> 'Registro fora da sequência'
			, '002'	=> 'Registro duplicado'
			, '003'	=> 'Tipo de registro inválido'
			, '004'	=> 'Registro não pertence a esta planilha'
			, '005'	=> 'BDU do cedente não numérico'
			, '006'	=> 'BDU do cedente não informado'
			, '007'	=> 'BDU do cedente inexistente'
			, '008'	=> 'Código cedente não informado'
			, '009'	=> 'Código cedente não numérico'
			, '010'	=> 'Código cedente zerado'
			, '011'	=> 'Código cedente inexistente'
			, '012'	=> 'Código formulário não informado'
			, '013'	=> 'Código formulário inexistente' // 014 falta
			, '015'	=> 'Form. Laser c/ cód. Barras - Informar cód. Cedente'
			, '016'	=> 'Informar a data de vencimento da parcela única e/ou data do primiro vencimento'
			, '017'	=> 'Informar o valor da parcela única e/ou valor das parcelas'
			, '018'	=> 'Form. Laser c/ cód. Barras - Informar tipo da moeda'
			, '019'	=> 'Form. Laser c/ cód. Barras - Informar cód. Documento' // 020, 021
			, '022'	=> 'Periodicidade de vencimento inválida'
			, '023'	=> 'Tipo de montagem inválido'
			, '024'	=> 'Tipo de montagem não informado'
			, '025'	=> 'Quantidade de carnês inválida'
			, '026'	=> 'Quantidade de carnês não informada'
			, '027'	=> 'Número parcela "De" inválido'
			, '028'	=> 'Número parcela "De" não informado'
			, '029'	=> 'Número parcela "Até" inválido'
			, '030'	=> 'Número parcela "Até" não informado'
			, '031'	=> 'Quantidade parcelas inválida'
			, '032'	=> 'Quantidade parcelas não informada'
			, '033'	=> 'Parcela "De" maior que parcela "Até"'
			, '034'	=> 'Data primeiro vencimento inválida'
			, '035'	=> 'Data primeiro vencimento anterior à data de hoje'
			, '036'	=> 'Data primeiro vencimento já informada no Capa'
			, '037'	=> 'Data vencimento parcela única inválida'
			, '038'	=> 'Data vencimento parcela única anterior à data de hoje'
			, '039'	=> 'Data vencimento parcela única já informado no Capa'
			, '040'	=> 'Tipo de moeda inválido' // 041
			, '042'	=> 'Valor da parcela inválido'
			, '043'	=> 'Valor da parcela não informado'
			, '044'	=> 'Valor da parcela já informado no Capa'
			, '045'	=> 'Valor total das parcelas inválido'
			, '046'	=> 'Valor total das parcelas não informado'
			, '047'	=> 'Valor da parcela única inválido'
			, '048'	=> 'Valor da parcela única não informado'
			, '049'	=> 'Valor da parcela única já informado no Capa'
			, '050'	=> 'Valor total da parcela única inválido'
			, '051'	=> 'Valor total da parcela única não informado' // 052-057
			, '058'	=> 'Código do documento inválido'
			, '059'	=> 'Código do documento não numérico'
			, '060'	=> 'Código do documento não informado' // 061-069
			, '070'	=> 'BDU do cedente zerado' // 071, 072
			, '073'	=> 'BDU do cedente já está encerrado' // 074
			, '075'	=> 'Cedente está cancelado'
			, '076'	=> 'Código do formulário cancelado ou zerado' // 077-079
			, '080'	=> 'Tipo de moeda deve ser igual em todos os detalhes'
			, '081'	=> 'Se o tipo de moeda foi informado, é obrigatório informar o "Valor Parc." e o "Total Valor da Parc."'
			, '082'	=> 'Se um dos valores foi informado, é obrigatório informar o tipo de moeda'
			, '083'	=> 'O campo "Qt. Parc." não pode ser superior ao resultado do cálculo (Parc. Até - Parc. De) + 1'
			, '084'	=> 'A quantidade de parcelas não pode ser superior a 60'
			, '085'	=> 'O preenchimento da "Qt. Parc." é obrigatório e tem que ser maior que zero' // 086
			, '087'	=> 'A quantidade total de parcelas a emitir (Qt. Parc. X Qt. Camês) ultrapassou 60.000'
			, '089'	=> 'Código material disponível somente para emissão empresa'
			, '090'	=> 'Tipo de moeda inválido'
			, '091'	=> 'Uso de parcela única indevido para quantidade parcela 1' // 092-095
			, '096'	=> 'Para bloqueto auto-envelopado, obrigatório informar o nome do sacado'
			, '097'	=> 'Para bloqueto auto-envelopado, obrigatório informar a rua do sacado'
			, '098'	=> 'Para bloqueto auto-envelopado, obrigatório informar o número da residência do sacado'
			, '099'	=> 'Para bloqueto auto-envelopado, obrigatório informar a cidade do sacado'
			, '100'	=> 'Para bloqueto auto-envelopado, obrigatório informar a UF do sacado' // 101-111
			, '112'	=> 'CEP do sacado inválido' // 113
			, '114'	=> 'Arquivo recusado por duplicidade'
			, '115'	=> 'Arquivo vazio'
			, '116'	=> 'Arquivo sem registros anexos'
			, '117'	=> 'Seqüência de registros inválida'
			, '118'	=> 'Código de registro diferente de zero'
			, '119'	=> 'Código de remessa diferente de um (1)'
			, '120'	=> 'Literal remessa diferente de (Remessa)'
			, '121'	=> 'Código de serviço diferente de um (1)'
			, '122'	=> 'Literal cobrança diferente de (Cobrança)'
			, '123'	=> 'Data de gravação não numérica'
			, '124'	=> 'Densidade de gravação diferente de 1600 e 6250'
			, '125'	=> 'Literal densidade diferente de (BPI)'
			, '126'	=> 'Hora de gravação não numérica'
			, '127'	=> 'Número seqüencial não numérico'
			, '128'	=> 'Número seqüência fora de seqüência'
			, '129'	=> 'Código do banco diferente de "399"'
			, '130'	=> 'Lote de serviços diferente de zeros'
			, '131'	=> 'Lote de serviços Header fora da seqüência'
			, '132'	=> 'Código Layout diferente de 020'
			, '133'	=> 'Tipo de registro Header inválido'
			, '134'	=> 'Tipo de operação diferente de R'
			, '135'	=> 'Tipo de serviço diferente de 02'
			, '136'	=> 'Forma de lançamento diferente de 00 (zeros)'
			, '137'	=> 'Versão de lote diferente de 010 (dez)'
			, '138'	=> 'Número de remessa não numérico'
			, '139'	=> 'Tipo de montagem só pode ser 0 (zero) ou 1 (um)'
			, '140'	=> 'Quantidade de Lotes diferente do informado no arquivo'
			, '141'	=> 'Quantidade de registro trailer do arquivo diferente do informado'
			, '142'	=> 'Arquivo sem o registro Header'
			, '143'	=> 'Arquivo sem o registro Trailer'
			, '144'	=> 'Arquivo sem os registros Header e Trailer'
			, '145'	=> 'Formulário incompatível com o códgo de postagem' // 146-150
			, '151'	=> 'Cedente sem cadastro de postagem no HSBC'
		);
		$cods	= array();
		if( array_key_exists($cod1, $codigos) ) {
			$cods[$cod1]= $codigos[$cod1];
		}
		if( array_key_exists($cod2, $codigos) ) {
			$cods[$cod2]= $codigos[$cod2];
		}
		if( array_key_exists($cod3, $codigos) ) {
			$cods[$cod3]= $codigos[$cod3];
		}
		return $cods;
	}

/**
 * Converte uma data no formato do arquivo (ddmmaa) para o formato de
 * data do banco (aaaa-mm-dd).
 * @param  string $data data no formato do arquivo.
 * @return string data no formato do banco.
 */
	function __converterData($data) {

		if( preg_match('/(0|1|2|3)[0-9](0|1)[0-9][0-9]{2}/', $data) ) {
			$dd	= substr($data, 0, 2);
			$mm	= substr($data, 2, 2);
			$aa	= substr($data, 4);
			return sprintf('20%s-%s-%s', $aa, $mm, $dd);
		}
		return null;
	}

/**
 * Converte um formato de valor do arquivo (nnnnnnnnnnnnn) para o formato float
 * com duas casas decimais.
 * @param  string $valor data no formato do arquivo.
 * @return float  valor no formato do banco.
 */
	function __converterValor($valor) {

		if( strlen($valor) == 13 ) {
			$int	= substr($valor, 0, 11);
			$dec	= substr($valor, 11);
			return (float) $int.'.'.$dec;
		}
		return null;
	}
}
