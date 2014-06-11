<?php
class Usuario extends AppModel {
	var $name			= 'Usuario';
	var $displayField	= 'nome_completo';
	var $hasMany		= array('Lancamento');
	var $validate		= array(
		  'usuario'		=> array(
			  'unico'	=> array('rule'=> 'isUnique', 'message'=> 'Este usuário já está cadastrado')
			, 'valido'	=> array('rule'=> 'alphaNumeric', 'required'=> true, 'allowEmpty'=> false, 'message'=> 'Obrigatório informar um usuário com letras e/ou números')
		  )
		, 'senha'		=> array('rule'=> 'alphaNumeric', 'required'=> true, 'allowEmpty'=> false, 'message'=> 'Obrigatório informar a senha')
	);

/** @see Model::beforeSave() */
	function beforeSave() {

		$this->data['Usuario']['nome_completo']	= strtoupper($this->data['Usuario']['nome_completo']);
		$this->data['Usuario']['usuario']		= strtolower($this->data['Usuario']['usuario']);
		return true;
	}
}
