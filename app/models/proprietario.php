<?php
class Proprietario extends AppModel {
	var $name			= 'Proprietario';
	var $displayField	= 'nome';
	var $hasMany		= array('Unidade');
	var $validate		= array(
		  'nome'		=> array('rule'=> array('minLength', 5), 'required'=> true, 'message'=> 'Um nome de proprietário real deve ser informado')
		, 'cpf_cnpj'	=> array(
			  'numerico'=> array('rule'=> 'numeric', 'required'=> true, 'message'=> 'CPF/CNPJ do proprietário deve conter apenas números')
			, 'tamanho'	=> array('rule'=> 'cpfcnpj', 'required'=> true, 'message'=> 'CPF/CNPJ do proprietário deve ter 11 ou 14 dígitos')
			, 'unico'	=> array('rule'=> 'isUnique', 'required'=> true, 'message'=> 'Já existe um proprietário cadastrado com este mesmo CPF/CNPJ')
		)
		, 'endereco'	=> array('rule'=> array('minLength', 5), 'required'=> true, 'message'=> 'O endereço do proprietário deve ser informado')
		, 'bairro'		=> array('rule'=> array('minLength', 1), 'required'=> true, 'message'=> 'O bairro do proprietário deve ser informado')
		, 'cep'			=> array('rule'=> 'numeric', 'required'=> true, 'message'=> 'O cep do proprietário deve ser informado')
		, 'cidade'		=> array('rule'=> array('minLength', 3), 'required'=> true, 'message'=> 'A cidade do proprietário deve ser informada')
		, 'uf'			=> array('rule'=> array('minLength', 2), 'required'=> true, 'message'=> 'A UF do proprietário deve ser informado')
		, 'fone1'		=> array('rule'=> array('between', 10, 10), 'required'=> true, 'message'=> 'Ao menos o primeiro número de telefone deve ser informado')
	);

/**
 * Método de validação.  Considera válido um CPF/CNPJ apenas pelo tamanho de 11 ou 14 dígitos.
 */
	function cpfcnpj($value, $params= array()) {
		$field	= current($value);
		$len	= strlen($field);
		return $len == 11 || $len == 14;
	}


/**
 * Uniformiza para gravar dados numéricos sempre sem formatação.
 * @return boolean true
 */
	function beforeValidate() {

		$naoNum		= '/[^0-9]/';

		if( isset($this->data[$this->alias]['cpf_cnpj']) ) {
			$this->data[$this->alias]['cpf_cnpj']		= preg_replace($naoNum, '', $this->data[$this->alias]['cpf_cnpj']);
		}
		if( isset($this->data[$this->alias]['conjuge_cpf']) ) {
			$this->data[$this->alias]['conjuge_cpf']	= preg_replace($naoNum, '', $this->data[$this->alias]['conjuge_cpf']);
		}
		if( isset($this->data[$this->alias]['cep']) ) {
			$this->data[$this->alias]['cep']			= preg_replace($naoNum, '', $this->data[$this->alias]['cep']);
		}
		if( isset($this->data[$this->alias]['conjuge_cep']) ) {
			$this->data[$this->alias]['conjuge_cep']	= preg_replace($naoNum, '', $this->data[$this->alias]['conjuge_cep']);
		}
		if( isset($this->data[$this->alias]['fone1']) ) {
			$this->data[$this->alias]['fone1']			= preg_replace($naoNum, '', $this->data[$this->alias]['fone1']);
		}
		if( isset($this->data[$this->alias]['conjuge_fone1']) ) {
			$this->data[$this->alias]['conjuge_fone1']	= preg_replace($naoNum, '', $this->data[$this->alias]['conjuge_fone1']);
		}
		if( isset($this->data[$this->alias]['fone2']) ) {
			$this->data[$this->alias]['fone2']			= preg_replace($naoNum, '', $this->data[$this->alias]['fone2']);
		}
		if( isset($this->data[$this->alias]['conjuge_fone2']) ) {
			$this->data[$this->alias]['conjuge_fone2']	= preg_replace($naoNum, '', $this->data[$this->alias]['conjuge_fone2']);
		}
		if( isset($this->data[$this->alias]['celular']) ) {
			$this->data[$this->alias]['celular']		= preg_replace($naoNum, '', $this->data[$this->alias]['celular']);
		}
		if( isset($this->data[$this->alias]['conjuge_celular']) ) {
			$this->data[$this->alias]['conjuge_celular']= preg_replace($naoNum, '', $this->data[$this->alias]['conjuge_celular']);
		}
		return true;
	}

/**
 * Uniformiza o nome em maiúsculas.
 */
	function beforeSave($options = array()) {
		$this->data['Proprietario']['nome'] = strtoupper($this->data['Proprietario']['nome']);
		return true;
	}
}
