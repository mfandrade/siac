<?php
class AppModel extends Model {
	var $actsAs		= array('Containable');

/** Model::__construct() */
	function __construct($id= false, $table= null, $ds= null) {
		parent::__construct($id, $table, $ds);
		// configurações
		Configure::load('siac.config');
	}

/** Model::beforeFind() */
	function beforeFind($query) {

		if( $query['conditions'] && is_array($query['conditions']) ) {

			array_walk_recursive($query['conditions'], array($this, '__formatarDatasBD'));
		}
		return $query;
	}

	function __formatarDatasBD(&$v, $k) {

		$v = preg_replace('%(\d{2})/(\d{2})/(\d{4})%', '$3-$2-$1', $v);
		return;
	}

/** Model::beforeValidate() */
	function beforeValidate() {

		// se achar tipos date, datetime ou float, converte para formato do banco
		foreach( $this->data as $key=> $valores ) {

			App::import('Model', $key);
			$model	=& new $key();
			$tipos	= $model->getColumnTypes();
			foreach( $tipos as $campo=> $tipo ) {

				if( in_array($tipo, array('integer', 'datetime', 'text')) ) continue;
				if( !array_key_exists($campo, $this->data[$key]) ) continue;
				$valor	= $this->data[$key][$campo];
				if( $tipo == 'date' ) {

					$valor	= preg_replace('%(\d{2})/(\d{2})/(\d{4})%', '$3-$2-$1', $valor);
				} elseif( $tipo == 'float' ) {

					$valor= str_replace(',', '.', $valor);
					if( is_numeric($valor) ) { // XXX: só um fixzinho para resolver o problema
						$valor= number_format($valor, 2, '.', '');
					}
				}
				$this->data[$key][$campo]	= $valor;
			}
			unset($model);
		}
		return true;
	}


// Validações diversas.  Vide: http://github.com/jrbasso/cake_ptbr/blob/master/models/behaviors/validacao.php
/*	function cpf($model, $data, $apenasNumeros= true) {
		if( isset($this->data[$model], ) )
		if ($extra) {
			return $this->_cpf(current($data), $apenasNumeros);
		}
		return $this->_cpf(current($data));
	}

	function _cpf($data, $apenasNumeros = true) {
		// Testar o formato da string
		if ($apenasNumeros) {
			if (!ctype_digit($data) || strlen($data) != 11) {
				return false;
			}
			$numeros = $data;
		} else {
			if (!preg_match('/\d{3}\.\d{3}\.\d{3}-\d{2}/', $data)) {
				return false;
			}
			$numeros = substr($data, 0, 3) . substr($data, 4, 3) . substr($data, 8, 3) . substr($data, 12, 2);
		}
		// Testar se todos os números estão iguais
		for ($i = 0; $i <= 9; $i++) {
			if (str_repeat($i, 11) == $numeros) {
				return false;
			}
		}
		// Testar o dígito verificador
		$dv = substr($numeros, -2);
		for ($pos = 9; $pos <= 10; $pos++) {
			$soma = 0;
			$posicao = $pos + 1;
			for ($i = 0; $i <= $pos - 1; $i++, $posicao--) {
				$soma += $numeros{$i} * $posicao;
			}
			$div = $soma % 11;
			if ($div < 2) {
				$numeros{$pos} = 0;
			} else {
				$numeros{$pos} = 11 - $div;
			}
		}
		$dvCorreto = $numeros{9} * 10 + $numeros{10};
		return $dvCorreto == $dv;
	}

	function cnpj($model, $data, $apenasNumeros = true, $extra = null) {
		if ($extra) {
			return $this->_cnpj(current($data), $apenasNumeros);
		} else {
			return $this->_cnpj(current($data));
		}
	}

	function _cnpj($data, $apenasNumeros = true) {
		// Testar o formato da string
		if ($apenasNumeros) {
			if (!ctype_digit($data) || strlen($data) != 14) {
				return false;
			}
			$numeros = $data;
		} else {
			if (!preg_match('/\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}/', $data)) {
				return false;
			}
			$numeros = substr($data, 0, 2) . substr($data, 3, 3) . substr($data, 7, 3) . substr($data, 11, 4) . substr($data, 16, 2);
		}
		// Testar o dígito verificador
		for ($pos = 12; $pos <= 13; $pos++) {
			$soma = 0;
			$mult = $pos - 7; // 5 ou 6
			for ($i = 0; $i < $pos; $i++) {
				$soma += $numeros{$i} * $mult--;
				if ($mult === 1) {
					$mult = 9;
				}
			}
			$div = $soma % 11;
			if ($div < 2) {
				$dvCorreto = 0;
			} else {
				$dvCorreto = 11 - $div;
			}
			if ($dvCorreto != $numeros{$pos}) {
				return false;
			}
		}
		return true;
	}

	function cep($model, $data, $separadores = null, $extra = null) {
		if ($extra) {
			return $this->_cep(current($data), $separadores);
		}
		return $this->_cep(current($data));
	}

	function _cep($data, $separadores = array('', '-')) {
		if (!is_array($separadores)) {
			$separadores = array($separadores);
		}
		if (strlen($data) < 8) {
			return false;
		} else {
			$numeros = preg_replace('/[^\d]/', '', $data);
			if (strlen($numeros) < 8) {
				return false;
			}
		}
		$primeiraParte = substr($numeros, 0, 5);
		$segundaParte = substr($numeros, -3);
		foreach ($separadores as $separador) {
			$formatado = $primeiraParte . $separador . $segundaParte;
			if ($formatado == $data) {
				return true;
			}
		}
		return false;
	}

	function telefone($model, $data, $apenasNumeros = true, $extra = null) {
		if ($extra) {
			return $this->_telefone(current($data), $apenasNumeros);
		}
		return $this->_telefone(current($data));
	}

	function _telefone($data, $apenasNumeros = true) {
		if ($apenasNumeros) {
			$tam = strlen($data);
			if ($tam == 8 || $tam == 10) {
				return true;
			}
			return false;
		}
		if (preg_match('/^\d{4}-\d{4}$/', $data)) { // 9999-9999
			return true;
		}
		if (preg_match('/^\(\d{2}\) ?\d{4}-\d{4}$/', $data)) { // (99) 9999-9999 ou (48)9999-9999
			return true;
		}
		if (preg_match('/^\+\d{2} ?\(\d{2}\) ?\d{4}-\d{4}$/', $data)) { // +55 (99) 9999-9999
			return true;
		}
		return false;
	}
*/

/**
 * Returna uma lista de UFs.
 * @param  boolean $agrupa   se deve ou não agrupar pelas regiões.
 * @param  string  $primeiro o texto do primeiro elemento, ou null se não quiser.
 * @return array   uma lista de UFs.
 */
	function listUfs($agrupa= false, $primeiro= 'SELECIONE...') {

		$norte		= array(
			  'AC'	=> 'ACRE'
			, 'AP'	=> 'AMAPÁ'
			, 'AM'	=> 'AMAZONAS'
			, 'PA'	=> 'PARÁ'
			, 'RO'	=> 'RONDÔNIA'
			, 'RR'	=> 'RORAIMA'
			, 'TO'	=> 'TOCANTINS'
		);
		$nordeste	= array(
			  'AL'	=> 'ALAGOAS'
			, 'BA'	=> 'BAHIA'
			, 'CE'	=> 'CEARÁ'
			, 'MA'	=> 'MARANHÃO'
			, 'PB'	=> 'PARAÍBA'
			, 'PE'	=> 'PERNAMBUCO'
			, 'PI'	=> 'PIAUÍ'
			, 'RN'	=> 'RIO GRANDE DO NORTE'
			, 'SE'	=> 'SERGIPE'
		);
		$centroOeste= array(
			  'DF'	=> 'DISTRITO FEDERAL'
			, 'GO'	=> 'GOIÁS'
			, 'MS'	=> 'MATO GROSSO DO SUL'
			, 'MT'	=> 'MATO GROSSO'
		);
		$sudeste	= array(
			  'ES'	=> 'ESPÍRITO SANTO'
			, 'MG'	=> 'MINAS GERAIS'
			, 'RJ'	=> 'RIO DE JANEIRO'
			, 'SP'	=> 'SÃO PAULO'
		);
		$sul		= array(
			  'PR'	=> 'PARANÁ'
			, 'RS'	=> 'RIO GRANDE DO SUL'
			, 'SC'	=> 'SANTA CATARINA'
		);
		if( $agrupa ) {
			$ufs	= array(
				  'NORTE'		=> $norte
				, 'NORDESTE'	=> $nordeste
				, 'CENTRO-OESTE'=> $centroOeste
				, 'SUDESTE'		=> $sudeste
				, 'SUL'			=> $sul
			);
			if( isset($primeiro) ) {
				$ufs	= array(''=> $primeiro) + $ufs;
			}
			return $ufs;
		}
		$ufs	= $norte + $nordeste + $centroOeste + $sudeste + $sul;
		asort($ufs);
		if( isset($primeiro) ) {
			$ufs	= array(''=> $primeiro) + $ufs;
		}
		return $ufs;
	}

}
