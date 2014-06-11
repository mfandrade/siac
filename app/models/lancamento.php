<?php
class Lancamento extends AppModel {
	var $name			= 'Lancamento';
	var $displayField	= 'mes_ano';
	var $validate		= array(
		  'mes_ano'				=> array(
			  'formato'				=> array('rule'=> '/^[0-9]{2}\/[0-9]{4}$/', 'required'=> true, 'message'=> 'Mês e ano devem ter um valor adequado no formato MM/AAAA')
			, 'nao_repete'			=> array('rule'=> array('noDuplicates', array('mes_ano', 'unidade_id', 'tipo_lancamento_id')), 'message'=> 'Já há um lançamento deste tipo para o mês e ano informados')
		  )
		, 'valor_documento'			=> array('rule'=> array('decimal', 2), 'required'=> true)
		, 'unidade_id'				=> array('rule'=> 'numeric', 'required'=> true)
		, 'tipo_lancamento_id'		=> array('rule'=> 'numeric', 'required'=> true)
		, 'dta_vencimento'			=> array('rule'=> 'date', 'required'=> true)
		, 'taxa_id'					=> array('numeric')
		, 'multa_id'				=> array('numeric')
		, 'usuario_id'				=> array('rule'=> 'numeric', 'required'=> true)
		, 'acordo_id'				=> array('numeric')
	);
	var $belongsTo		= array(
		  'Unidade'
		, 'TipoLancamento'
		, 'Taxa'
		, 'Multa'
		, 'InstrucaoBoleto'
		, 'Usuario'
		, 'Acordo'
	);
	var $hasOne			= array('Pagamento');

/**
 * Método de validação que impede registros com os mesmos valores dos campos dados.
 * @see    http://bin.cakephp.org/view/653776884
 * @param  mixed $value  valor do campo em questão
 * @param  array $params array contendo relação dos nomes de campos a considerar
 * @return boolean, dependendo se a lógica considera ou não válido o registro
 */
	function noDuplicates($value, $params) {

		if( !empty($this->id) ) {
			$conditions[]	= array($this->primaryKey . ' <>'=> $this->id);
		}
		foreach( $params as $field ) {

			if( isset($this->data[$this->name][$field]) ) {
				$fieldVal	= $this->data[$this->name][$field];
			} else {
				$fieldVal	= null;
			}
			$conditions[]	= array($field=> $fieldVal);
		}
		$existingFieldsCount= $this->find('count', array('conditions'=> $conditions, 'recursive'=> -1));

		return $existingFieldsCount < 1;
	}

/**
 * Calcula, a partir dos parâmetros de configuração e da data atual
 * do sistema, qual o próximo mês e ano pretendidos para o próximo
 * lançamento.
 * @param int $dia			dia de vencimento no mês
 * @param int $antecedencia	até quantos dias antes considerar
 * @return string mês e ano no formato mm/aaaa
 */
	function obterMesAnoDefaultLancamento($dia, $antecedencia) {

		$diaAtual	= date('d');
		$mesAtual	= date('m');
		$anoAtual	= date('Y');
		if( $dia - $antecedencia > $diaAtual ) {

			return implode('/', array($mesAtual, $anoAtual));
		}
		$ano	= ( $mesAtual == 12 )? $anoAtual+1: $anoAtual;
		$mes	= str_pad(($mesAtual+1) % 12, 2, '0', STR_PAD_LEFT);

		return implode('/', array($mes, $ano));
	}

/**
 * Retorna um array de opções para o meses de lançamento.
 * @param  integer $dia          o dia esperado para vencimento
 * @param  integer $antecedencia dias a considerar antes do lançamento para o mês
 * @param  integer $meses        quantidade de meses a retornar
 * @return array de índices alfabéticos contento a relação de meses e anos no id e valor no formato MM/AAAA
 */
	function obterMesesAnosLancamento($dia, $antecedencia, $meses= 6, $tipo= TIPO_LANCAMENTO_TAXACONDOMINIAL) {

		$mesAno			= $this->obterMesAnoDefaultLancamento($dia, $antecedencia);
		list($mes, $ano)= explode('/', $mesAno);
		$time			= mktime(12, 0, 0, $mes, $dia, $ano);

		$this->Unidade->contain();
		$qtdUnidades	= $this->Unidade->find('count');

		$i	= 0;
		$opcoes			= array();
		do {

			$mesAno			= date('m/Y', $time);
			$this->contain();
			$qtdLancamentos	= $this->find('count', array('conditions'=> array('Lancamento.mes_ano'=> $mesAno, 'Lancamento.tipo_lancamento_id'=> $tipo)));

			if( $qtdUnidades != $qtdLancamentos ) {

				$opcoes[$mesAno]	= $mesAno;
			}
			$time			= strtotime('+1 month', $time);
		} while( ++$i < $meses );

		return $opcoes;
	}

/**
 * Retorna um array com os meses já lançados
 * @param  integer $tipo  tipo de lançamento a considerar
 * @param  integer $meses quantidade de meses a retornar (no máximo)
 * @return array de índices alfabéticos contento a relação de meses e anos no id e valor no formato MM/AAAA
 */
	function obterMesesAnosLancados($tipo= TIPO_LANCAMENTO_QUALQUER, $meses= 6) {

//		$this->Unidade->contain();
//		$qtdUnidades	= $this->Unidade->find('count');
		$this->contain();
        $condicoes  = array('1=1');
        if( $tipo !== TIPO_LANCAMENTO_QUALQUER ) {
            $condicoes = array('Lancamento.tipo_lancamento_id'=> $tipo);
        }
		$mesesAnos= $this->find('all', array('fields'=> array('Lancamento.mes_ano', 'COUNT(Lancamento.mes_ano) as count')
			, 'conditions'=> $condicoes
			, 'group'=> 'Lancamento.mes_ano'
			, 'order'=> 'Lancamento.dta_vencimento DESC'
		));
		$mesesAnos= Set::combine($mesesAnos, '{n}.Lancamento.mes_ano', '{n}.Lancamento.mes_ano');
		return $mesesAnos;
	}


/**
 * Realiza os lançamentos para todas as unidades a partir dos dados
 * que são especificados principalmente pelo formulário.
 *
 * @param  array $dados array de dados do lançamento, incluindo as chaves
 *                      "mes_ano", "valor_documento", "tipo_lancamento_id",
 *                      "usuario_lancamento_id".
 * @param  int   $qtd   quantidade de lançamentos consecutivos a fazer
 * @return true se conseguir, false em caso contrário ou ainda se os dados não validarem
 */
	function efetuarLancamentos($dados, $qtd= 1) {

		// obtém a data de vencimento
		switch( $dados['Lancamento']['tipo_lancamento_id'] ) {
			default:
			case TIPO_LANCAMENTO_TAXACONDOMINIAL:
				$dia	= Configure::read('lancamentos_taxacondominial_vencimento_dia');
				$qtd	= 1;
				break;
			case TIPO_LANCAMENTO_TAXAEXTRA:
				$dia	= Configure::read('lancamentos_taxaextra_vencimento_dia');
				// $qtd	= $qtd; // permite lançamentos consecutivos apenas para taxa
				break;
			case TIPO_LANCAMENTO_MULTAINFRACAO:
				$dia	= Configure::read('lancamentos_multainfracao_vencimento_dia');
				$qtd	= 1;
				break;
		}
		$i= 0;
		do {
			list($mes, $ano)	= explode('/', $dados['Lancamento']['mes_ano']);
			$dta_vencimento		= sprintf('%s-%s-%s', $ano, $mes, $dia);
			$dados['Lancamento']['dta_vencimento']	= $dta_vencimento;

			// apenas para dar as mensagens de validação
			$dados['Lancamento']['unidade_id']		= 1;
			$this->set($dados);
			if( !$this->validates() ) {
				return false;
			}
			// expande os dados necessários para todas as unidades
			$this->data	= null;
			$unidades	= $this->Unidade->find('list', array('order'=> 'id'));
			foreach( $unidades as $unidade ) {

				$dados['Lancamento']['unidade_id']	= $unidade;
				$this->data[]	= $dados;
			}
			// avança o mês
			$time	= mktime(12, 0, 0, $mes, $dia, $ano);
			$time	= strtotime('+1 month', $time);
			$dados['Lancamento']['mes_ano']	= date('m/Y', $time);

		} while( ++$i < $qtd );

		return $this->saveAll($this->data);
	}

/**
 * Retorna os registros lançados e não pagos do tipo dado para a
 * unidade em questão.
 * @param  int $unidade_id		id da unidade
 * @param  array $meses_anos	array com meses/anos a considerar
 * @param  int $tipo_unidade_id	id do tipo de pagamento
 * @return array uma lista dos id/mes_ano dos registros encontrados
 */
	function obterLancamentosAberto($unidade_id= null, $meses_anos= array(), $tipo_lancamento_id= TIPO_LANCAMENTO_TAXACONDOMINIAL) {

		if( !$unidade_id )  {

			$emAberto	= array();
			$this->Unidade->Behaviors->attach('Containable');
			$this->Unidade->contain();
			$unidades	= $this->Unidade->find('list', array('order'=> array('Unidade.quadra_id', 'Unidade.lote')));
			$this->Unidade->Behaviors->detach('Containable');
			foreach( $unidades as $unidade ) {
				// err... ahn... pog
				if( $unidade == 473 ) continue;
				$emAberto+= $this->__obterLancamentosAbertoUnidade($unidade, $meses_anos, $tipo_lancamento_id);
			}
			return $emAberto;
		}
		$emAberto	= $this->__obterLancamentosAbertoUnidade($unidade_id, $meses_anos, $tipo_lancamento_id);
		return $emAberto;
	}

	function __obterLancamentosAbertoUnidade($unidade_id, $meses_anos= array(), $tipo_lancamento_id= TIPO_LANCAMENTO_TAXACONDOMINIAL) {

		$condicoes	= array(
			  'Lancamento.unidade_id'=> $unidade_id
		);
		if( !empty($meses_anos) ) {
			$condicoes+= array('Lancamento.mes_ano IN (\''.implode('\',\'', $meses_anos).'\')');
		}
		if( $tipo_lancamento_id != TIPO_LANCAMENTO_QUALQUER ) {
			$condicoes+= array('Lancamento.tipo_lancamento_id'=> $tipo_lancamento_id);
		}
		// acha todos os lancamentos do tipo para a unidade em questao
		// todos os dados são necessários, para os relatórios de inadimplência e posição financeira
		// "o que abunda não prejudica"
		$this->contain(array('Pagamento', 'TipoLancamento', 'Unidade', 'Unidade.Quadra', 'Unidade.Proprietario'));
		$lancamentos	= $this->find('all', array('conditions'=> $condicoes, 'order'=> 'Lancamento.created'));

		// desconsidera estes lancamentos que estiverem pagos
		$emAberto	= array();
		$this->Pagamento->contain();
		foreach( $lancamentos as $lancamento ) {
			// se nao achou um pagamento, o lancamento está aberto e nos interessa
			$pago = $this->Pagamento->find('all', array(
				  'fields'=> 'Pagamento.lancamento_id'
				, 'conditions'=> array('Pagamento.lancamento_id'=> $lancamento['Lancamento']['id'])
			));
			if( !$pago ) {

				$lancamento['Pagamento']	= $this->Pagamento->calcularValorAcrescimo(
					  $lancamento['Lancamento']['valor_documento']
					, $lancamento['Lancamento']['dta_vencimento']
					, date('d/m/Y')
					, $lancamento['Lancamento']['tipo_lancamento_id']);
				$emAberto[$lancamento['Lancamento']['id']]	= $lancamento;
			}
		}

		return $emAberto;
	}
}
