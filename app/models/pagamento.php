<?php
class Pagamento extends AppModel {
	var $name		= 'Pagamento';
	var $validate	= array(
		  'dta_pagamento'		=> array(
			  'data'			=> array('rule'=> 'date', 'required'=> true, 'message'=> 'A data de pagamento é obrigatória')
//			, 'nao_retroativa'	=> array('rule'=> 'naoRetroativa', 'message'=> 'A data de pagamento não pode ser retroativa')
		)
		, 'valor_documento'		=> array('rule'=> array('decimal', 2), 'allowEmpty'=> true)
		, 'valor_desconto'		=> array('rule'=> array('decimal', 2), 'allowEmpty'=> true)
		, 'valor_acrescimo'		=> array('rule'=> array('decimal', 2), 'allowEmpty'=> true)
		, 'valor_pago'			=> array(
			  'moeda'			=> array('rule'=> array('decimal', 2), 'required'=> true)
			, 'consistente'		=> array('rule'=> array('totalizar', array('valor_documento', 'valor_desconto', 'valor_acrescimo')), 'message'=> 'O valor pago informado não corresponde ao valor de lançamento')
		)
		, 'parcela'				=> array('rule'=> 'numeric', 'required'=> false)
		, 'lancamento_id'		=> array(
			  'numeric'			=> array('rule'=> 'numeric', 'required'=> true, 'allowEmpty'=> false)
			, 'nao_repete'		=> array('rule'=> 'isUnique', 'message'=> 'O lançamento em questão já consta como pago')
		)
	);
		var $belongsTo	= array('Lancamento', 'ArquivoRetorno');

/**
 * Método de validação.  Verifica se o primeiro valor informado nos
 * params, menos o segundo e mais o terceiro resulta no valor da coluna
 * em questão.
 *
 * @param array $field	um array com nome do campo e valor da coluna em questão.
 * @param array $params	um array contendo, respectivamente, os nomes dos
 * 						campos original, descontos e acréscimos.
 * @return boolean 		true se corresponder, false em caso contrário.
 */
	function totalizar($field, $params) {

		$valor	= (int) number_format($this->data[$this->alias][$params[0]], 2, '.', '');
		$menos	= (int) number_format($this->data[$this->alias][$params[1]], 2, '.', '');
		$mais	= (int) number_format($this->data[$this->alias][$params[2]], 2, '.', '');
		$total	= (int) number_format(current($field), 2, '.', '');

		return ($valor-$menos+$mais == $total);
	}

/**
 * Método de validação.  Retorna verdadeiro se a data informada não
 * for anterior à data de hoje.
 *
 * @param array		chave/valor referente à data no formato Y-m-d
 * @return boolean	true se for maior ou igual à data de hoje, false em caso contrário.
 */
	function naoRetroativa($value) {

		$hoje	= date('Y-m-d');
		$field	= current($value);
		return ($field >= $hoje);
	}

/**
 * Recebe um arquivo, salva-o, trata-o, extrai os pagamentos e salva-os.
 * @param  mixed   $arq        string, com caminho do arquivo, ou array, com campo referente ao upload do arquivo.
 * @param  integer $usuario_id o id do usuário autenticado
 * @param  boolean $force      se deve ou não forçar a ler todo o arquivo caso encontre um problema em algum registro.
 * @return mixed   a  boolean false,  mes_ano, quadra_lote, valor, processado
 */
	function processarArquivo($arq, $usuario_id, $force= false) {

		if( is_array($arq) ) {
			$filename		= strtoupper($arq['name']);
			$filepath		= $arq['tmp_name'];

		} elseif( is_string($arq) && file_exists($arq) ) {
			$filepath		= $arq;
			$filename		= strtoupper(basename($arq));
		} else return false;

		$dta_gravacao	= $this->ArquivoRetorno->obterDataGravacao($filepath);

		$id		= false;
		$salvou	= false;
		$this->begin($this->name);
		{
			$count	= $this->ArquivoRetorno->find('count', array(
				  'conditions'=> array(
					  'and'=> array(
						  'ArquivoRetorno.dta_gravacao'=> $dta_gravacao
						, 'ArquivoRetorno.processado'=> true
					)
				)
			));
			if( $count > 0 ) {
				// TODO: erro arquivo já processado
			}
			$a['ArquivoRetorno']['dta_gravacao']	= $dta_gravacao;
			$a['ArquivoRetorno']['arquivo']			= $filename;
			$a['ArquivoRetorno']['processado']		= false;
			$this->ArquivoRetorno->save($a);

			$arquivo	= $this->ArquivoRetorno->id;
			$pagamentos	= $this->ArquivoRetorno->extrairPagamentos400($arq, $usuario_id, $arquivo, $force);
			if( empty($pagamentos) ) {

				$dados	= false;
			} else {

				$dados	= $this->saveAll($pagamentos, array('validate'=> 'first', 'atomic'=> false));
				if( !empty($this->validationErrors) ) {

					$regs	= array_keys($this->validationErrors);
				}
			}
		}
		if( $arquivo && $dados ) {
			$this->ArquivoRetorno->saveField('processado', true);
			$this->commit($this->name);
			return sizeof($pagamentos);
		}
		$this->rollback($this->name);
		return false;
	}

/**
 * Faz o cálculo dos componentes do acréscimo: juros e multa.  Inclui uma lógica de
 * negócio: para pagamentos feitos após o vencimento mas ainda dentro do mês, inclui
 * abono dos juros e multa.
 * Utiliza as constantes configuradas:
 * 	- pagamentos_atraso_porcentagem_multa				- (integer) % de multa após o vencimento
 * 	- pagamentos_atraso_porcentagem_juros_ad			- (integer) % de juros ad após o vencimento
 * 	- pagamentos_autoabono_juros_multa					- (boolean) se deve utilizar abono automático
 * 	- lancamentos_taxacondominial_valor_descontopadrao	- (float) valor de desconto para taxa condominial no vencimento
 * @param  float   $valor		o valor do documento
 * @param  string  $vencimento	a data do vencimento no formato de banco
 * @param  integer $tipo		tipo do lançamento
 * @param  string  $pagamento	a data de pagamento no formato de banco (default, hoje)
 * @return array   um array associativo com as chaves valor_juros, valor_multa e seus respectivos valores.
 */
	function calcularValorAcrescimo($valor, $vencimento, $pagamento = null, $tipo= TIPO_LANCAMENTO_TAXACONDOMINIAL) {

		$multa		= 0.00;
		$juros		= 0.00;
		$desconto	= 0.00;

		$pMulta	= Configure::read('pagamentos_atraso_porcentagem_multa');
		$pJuros	= Configure::read('pagamentos_atraso_porcentagem_juros_ad');
		if( !isset($pMulta) ) { $pMulta	= 2; }
		if( !isset($pJuros) ) { $pJuros	= 0.033; }

		$autoAbono	= Configure::read('pagamentos_atraso_autoabono');


		if( preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $vencimento) ) {

			list($ano, $mes, $dia)	= explode('-', $vencimento);
			$vencimento	= sprintf('%s/%s/%s', $dia, $mes, $ano);
		}
		if( $pagamento == null ) {

			$pagamento	= date('d/m/Y');
		}
		$atraso	= ClassRegistry::init('Boleto')->dateDiff($pagamento, $vencimento, false);
		$atraso	= ($atraso > 0)? $atraso: 0;

		if( $atraso > 0 ) {

			$multa		= round(round($valor * $pMulta/100, 2), 2);
			$juros		= round(round($valor * $pJuros/100, 2) * $atraso, 2);

			list($diav, $mesv, $anov)	= explode('/', $vencimento);
			list($diap, $mesp, $anop)	= explode('/', $pagamento);
			if( $autoAbono && ($mesp == $mesv) ) {

				$desconto	= $multa+$juros;
			}
		} else {
			if( $tipo == TIPO_LANCAMENTO_TAXACONDOMINIAL ) {
				$desconto	= Configure::read('lancamentos_taxacondominial_valor_descontopadrao');
			}
		}
		return array('valor_juros'=> $juros, 'p_juros'=> $pJuros, 'valor_multa'=> $multa, 'p_multa'=> $pMulta, 'valor_desconto'=> $desconto, 'atraso'=> $atraso);
	}

/**
 * Corrige problemas com o driver dbo_sqlite3.  P.ex., inserir a string
 * "NULL" ao invés do valor null.
 * 
 * Corrige também um problema relacionado a casas decimais.  Garante que
 * sempre terá separador ponto vindo do banco.
 */
	function beforeSave() {

		foreach( $this->data['Pagamento'] as $k=> $v ) {

			if( $v === 'NULL' ) {
				$this->data['Pagamento'][$k]= null;
			}			

			if( in_array($k, array('valor_desconto', 'valor_acrescimo', 'valor_pago', 'valor_documento', 'cheque', 'dinheiro')) ) {
				$this->data['Pagamento'][$k]= str_replace(',', '.', $this->data['Pagamento'][$k]);
			}
		}		
		return true;
	}
}
