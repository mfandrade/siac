<?php
class TaxasController extends AppController {
	var $name		= 'Taxas';
	var $helpers	= array('Javascript');

/**
 * Efetua o cadastro e os lançamentos de taxa extra para todas as unidades.
 */
	function cadastrar() {

		$hoje		= date('d/m/Y');
		$qtds		= array(1=> 1, 2=> 2, 3=> 3, 4=> 4, 5=> 5, 6=> 6);	// TODO: definir constante
		$mesesAnos	= $this->__obterProximosMeses();
		$this->set(compact('hoje', 'qtds', 'mesesAnos'));

		if( !empty($this->data) ) {

			$user	= $this->Auth->user();
			$done	= $this->Taxa->cadastrarLancar($this->data, $user['Usuario']['id']);
		}
	}

	function __obterProximosMeses($qtd= 3, $atual= null) {

		$mes	= date('m');
		$ano	= date('Y');
		if( !empty($atual) && preg_match('/\d{2}\/\d{4}/', $atual) ) {

			$mesAno	= explode('/', $atual);
			$mes	= $mesAno[0];
			$ano	= $mesAno[1];
		}
		$res	= array();
		for( $i= 0; $i < $qtd; $i++ ) {

			$mesAno			= sprintf('%02d', $mes).'/'.$ano;
			$res[$mesAno]	= $mesAno;

			$mes++;
			if( $mes == 13 ) {
				$mes= '01';
				$ano++;
			}
		}
$res= array('06/2009'=> '06/2009');
		return $res;
	}
}










