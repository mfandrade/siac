<?php
class TipoLancamento extends AppModel {
	var $name			= 'TipoLancamento';
	var $primaryKey		= 'cod';
	var $displayField	= 'descricao';
	var $hasMany		= array('Lancamento', 'InstrucaoBoleto');

/**
 * A regra de formação do nosso número inclui um dígito (5o) para o
 * tipo de lançamento.
 * @param $numero	um valor de nosso número dado
 * @return 	uma constante 0,1,2 dependendo do tipo de lançamento ou false
 */
	function findByNossoNumero($numero) {

		// reedita a regra de validação tanto para número documento quanto para nosso número
		$tamanho	= strlen($numero);
		if( $tamanho == 13 || $tamanho == 16 ) {
			$tipo	= substr($numero, 4, 1);
			if( in_array($tipo, array('0', '1', '2')) ) {
				return $tipo;
			}
		}
		return null;
	}
}
