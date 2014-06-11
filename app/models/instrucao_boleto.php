<?php
class InstrucaoBoleto extends AppModel {
	var $name			= 'InstrucaoBoleto';
	var $displayField	= 'texto';
	var $hasMany		= array('Lancamento');
	var $belongsTo		= array('TipoLancamento');

/**
 * Garante que as quebras de linha serão respeitadas ao salvar.
 * @link   http://book.cakephp.org/view/683/beforeSave
 * @param  $options ???
 * @return true
 */
	function beforeSave($options = array()) {
		
		if( array_key_exists($this->alias, $this->data) ) {
			
			$text	= $this->data[$this->alias]['texto'];
			$text	= strtr($text, array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />'));
			$this->data[$this->alias]['texto']	= $text;
		} elseif( array_key_exists(0, $this->data) ) {			
			foreach( $this->data as $n=> $instrucao ) {
				
				$text	= $this->data[$n][$this->alias]['texto'];
				$text	= strtr($text, array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />'));				
				$this->data[$n][$this->alias]['texto']	= $text;
			}
		}
		
		return true;
	}
}
