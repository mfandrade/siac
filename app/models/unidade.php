<?php
class Unidade extends AppModel {
	var $name		= 'Unidade';
	var $hasMany	= array('Lancamento');
	var $belongsTo	= array('Rua', 'Quadra', 'Proprietario');
	var $validate	= array(
		  'rua_id'			=> array('rule'=> 'alphaNumeric', 'required'=> true)
		, 'quadra_id'		=> array('rule'=> 'numeric', 'required'=> true)
		, 'lote'			=> array('rule'=> 'numeric', 'required'=> true)
		, 'proprietario_id'	=> array('rule'=> 'numeric', 'required'=> true)
//		, 'morador_cpf'		=> array('rule'=> 'cpf', 'required'=> false, 'allowEmpty'=> true, 'message'=> 'CPF do morador é inválido')
//		, 'morador_fone1'	=> array('rule'=> 'telefone', 'required'=> false, 'allowEmpty'=> true, 'message'=> 'Telefone1 do morador está num formato inválido')
//		, 'morador_fone2'	=> array('rule'=> 'telefone', 'required'=> false, 'allowEmpty'=> true, 'message'=> 'Telefone2 do morador está num formato inválido')
//		, 'morador_celular'	=> array('rule'=> 'telefone', 'required'=> false, 'allowEmpty'=> true, 'message'=> 'Celular do morador está num formato inválido')
		, 'morador_email'	=> array('rule'=> 'email', 'required'=> false, 'allowEmpty'=> true, 'message'=> 'E-mail do morador está num formato inválido')
	);

/**
 * Uniformiza para gravar cpf/cnpj e telefones sempre sem formatação.
 * @return boolean true
 */
	function beforeValidate() {

		$naoNum	= '/[^0-9]/';

		if( isset($this->data[$this->alias]['morador_cpf']) ) {
			$this->data[$this->alias]['morador_cpf']	= preg_replace($naoNum, '', $this->data[$this->alias]['morador_cpf']);
		}
		if( isset($this->data[$this->alias]['morador_fone1']) ) {
			$this->data[$this->alias]['morador_fone1']	= preg_replace($naoNum, '', $this->data[$this->alias]['morador_fone1']);
		}
		if( isset($this->data[$this->alias]['morador_fone2']) ) {
			$this->data[$this->alias]['morador_fone2']	= preg_replace($naoNum, '', $this->data[$this->alias]['morador_fone2']);
		}
		if( isset($this->data[$this->alias]['morador_celular']) ) {
			$this->data[$this->alias]['morador_celular']= preg_replace($naoNum, '', $this->data[$this->alias]['morador_celular']);
		}
		if( isset($this->data[$this->alias]['morador_cep']) ) {
			$this->data[$this->alias]['morador_cep']	= preg_replace($naoNum, '', $this->data[$this->alias]['morador_cep']);
		}
		return true;
	}

/**
 * Formata a quadra e lote sempre.
 * @param  $results array de resultados obtido
 * @param  $primary se está sendo buscado diretamente ou via associação
 * @return o array de resultados da operação
 */
	function afterFind($results, $primary= false) {
		if( isset($results[$this->alias]['quadra_id']) ) {
			$results[$this->alias]['quadra_id']	= sprintf('%02d', $results[$this->alias]['quadra_id']);
		}
		if( isset($results[$this->alias]['lote']) ) {
			$results[$this->alias]['lote']		= sprintf('%02d', $results[$this->alias]['lote']);
		}
		return $results;
	}

/**
 * Mimetiza os métodos "find", já utilizando quadra_id e lote a partir
 * do nosso número (ou número documento).
 * @param  string  $numero	número do documento ou nosso número
 * @return integer registro da unidade correspondente à quadra e lote
 *                 do número dado, ou false caso número seja incorreto
 *                 ou se não houver a unidade.
 */
	function findByNossoNumero($numero) {

		// reedita a regra de validação tanto para número documento quanto para nosso número
		$tamanho	= strlen($numero);
		if( $tamanho == 13 || $tamanho == 16 ) {
			$quadra	= substr($numero, 5, 2);
			$lote	= substr($numero, 7, 2);
			$this->contain();
			$res	= $this->find('first', array('conditions'=> array(
				  'and'=> array(
					  'Unidade.quadra_id'=> $quadra
					, 'Unidade.lote'=> $lote
			))));
			if( sizeof($res) == 1 ) return $res;
		}
		return false;
	}

/**
 *
 */
	function findList($agrupa= true) {

		$this->contain(array('Quadra', 'Proprietario'));
		$unidades	   = $this->find('all', array(
			  'fields'		=> array('Unidade.id', 'Unidade.quadra_id', 'Unidade.lote', 'Quadra.descricao', 'Quadra.abbr', 'Proprietario.nome')
			, 'order'		=> array('Unidade.quadra_id', 'Unidade.lote')
			, 'conditions'	=> array('Quadra.abbr IS NOT NULL')
		));
		if( $agrupa ) {
			$unidades	   = Set::combine($unidades, '{n}.Unidade.id', array('% 3sL%02d - %s', '{n}.Quadra.abbr', '{n}.Unidade.lote', '{n}.Proprietario.nome'), '{n}.Quadra.descricao');
		} else {
			$unidades	   = Set::combine($unidades, '{n}.Unidade.id', array('% 3sL%02d - %s', '{n}.Quadra.abbr', '{n}.Unidade.lote', '{n}.Proprietario.nome'));
		}
		$unidades	   = array(__('TODAS', true)) + $unidades;

		return $unidades;
	}

/**
 * Retorna um array contendo os id's das unidades referentes à
 * próxima quadra, próximo lote, quadra anterior e lote anterior.
 * @param	array $unidade	um registro de unidade atual, com os dados de quadra
 * @param	array $unidades	um conjunto de unidades já obtido
 * @return	array contendo as chaves 'prox_quadra', 'ante_quadra', 'prox_lote', 'ante_lote'.
 */
	function findNeighbours($unidade, $unidades= array()) {

		if( !isset($unidades) ) {

			$this->contain('Quadra');
			$unidades	= $this->find('all', array('order'=> array('Unidade.quadra_id', 'Unidade.lote')));
		}

		$idAtual= $unidade['Unidade']['id'];
		$qAtual	= $unidade['Unidade']['quadra_id'];
		$lAtual	= $unidade['Unidade']['lote'];
		$lTotal	= $unidade['Quadra']['total_lotes'];

		$quadras= $this->__retornaIdUnidadeQuadrasVizinhas($qAtual, $unidades);
		$lotes	= $this->__retornaIdUnidadeLotesVizinhos($qAtual, $lAtual, $unidades);
		if( !empty($quadras) && !empty($lotes) ) {
			return array('quadra'=> $quadras, 'lote'=> $lotes);
		}
		trigger_error(__('Estranhamente não foi possível determinar as quadras e lotes vizinhos.', true));
		return false;
	}

	function __retornaIdUnidadeQuadrasVizinhas($qAtual, $unidades) {

		// quadra 20 é a finada de acordos. só existe para processamento dos boletos antigos.
		if( $qAtual == 18 ) {

			$qProx	= 99;	// 99 é a comercial
			$qAnte	= $qAtual-1;
		} elseif( $qAtual == 1 ) {

			$qProx	= $qAtual+1;
			$qAnte	= 99;
		} elseif( $qAtual == 99 ) {

			$qProx	= 1;
			$qAnte	= 18;
		} elseif( $qAtual > 0 && $qAtual < 20 ) {

			$qProx	= $qAtual+1;
			$qAnte	= $qAtual-1;
		}
		$quadras	= array();
		foreach( $unidades as $unidade ) {

			if( ($qAnte == $unidade['Unidade']['quadra_id']) && ($unidade['Unidade']['lote'] == 1) ) {

				$quadras['prev']	= $unidade['Unidade']['id'];
				continue;
			}
			if( ($qProx == $unidade['Unidade']['quadra_id']) && ($unidade['Unidade']['lote'] == 1) ) {

				$quadras['next']	= $unidade['Unidade']['id'];
				continue;
			}
			if( isset($quadras['next']) && isset($quadras['prev']) ) {
				break;
			}
		}
		return $quadras;
	}

	function __retornaIdUnidadeLotesVizinhos($qAtual, $lAtual, $unidades) {

		$proxLote	= $lAtual+1;
		$anteLote	= $lAtual-1;

		$lotes		= array();
		$anterior	= $unidades[sizeof($unidades)-1]; // em sentido crescente, o anterior é o último
		foreach( $unidades as $unidade ) {

			if( $proxLote > $unidade['Quadra']['total_lotes'] ) {
				$proxLote	= 1;
			}
			if( $anteLote < 1 ) {
				$anteLote	= $anterior['Quadra']['total_lotes'];
			}
			$anterior	= $unidade; // salva o anterior

			if( ($proxLote == $unidade['Unidade']['lote']) && ($unidade['Unidade']['quadra_id'] == $qAtual) ) {

				$lotes['next']	= $unidade['Unidade']['id'];
				continue;
			}
			if( ($anteLote == $unidade['Unidade']['lote']) && ($unidade['Unidade']['quadra_id'] == $qAtual) ) {

				$lotes['prev']	= $unidade['Unidade']['id'];
				continue;
			}
			//
			if( isset($lotes['next']) && isset($lotes['prev']) ) {
				break;
			}
		}
		return $lotes;
	}
}
