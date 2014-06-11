<?php
/*
 * InserirDadosUnidades Migration
 * 09/30/2009 19:16:20
 */
App::import('Model', 'Unidade');
class InserirDadosUnidades extends AppMigration {

	// do something!
	function up() {
		$this->Unidade		=& ClassRegistry::init('Unidade');
		$this->Unidade->Proprietario->query('CREATE UNIQUE INDEX i_proprietarios_cpf_cnpj USING BTREE ON proprietarios(cpf_cnpj)');
		$this->Unidade->Proprietario->deleteAll(true);
		$this->Unidade->Proprietario->query('ALTER TABLE proprietarios auto_increment=0');

		// cadastra um proprietário default
		$this->data	= array('Proprietario'=> array(
			  'cpf_cnpj'=> '11111111111'
			, 'nome'	=> '***PROPRIETÁRIO NÃO CADASTRADO***'
			, 'rg' 		=> '000000 SSP/PA'
			, 'endereco'=> 'ROD. ARTUR BERNARDES, 1650'
			, 'bairro' 	=> 'PRATINHA I'
			, 'cep' 	=> '66825-000'
			, 'cidade' 	=> 'BELÉM'
			, 'uf' 		=> 'PA'
			, 'fone1' 	=> '9132222222'
		));
		$this->data	= $this->Unidade->Proprietario->create($this->data);
		$this->Unidade->Proprietario->save($this->data);

		$file				= APP . DS . 'config/sql/migrations/proprietarios.csv';
		$file				= str_replace('/', DS, $file);
		$dadosProprietarios	= file($file);
		foreach( $dadosProprietarios as $dadosProprietario ) {

			$linha		= explode('|', $dadosProprietario);
			$cpfCnpj	= trim($linha[4]);
			if( empty($cpfCnpj) ) {
				$cpfCnpj= '11111111111';
			}
			$this->Unidade->Proprietario->contain();
			$proprietario	= $this->Unidade->Proprietario->find('first', array(
				  'conditions'=> array(
					  'Proprietario.cpf_cnpj'	=> $cpfCnpj
				)
			));
			if( empty($proprietario) ) {
				
				$telefone	= $linha[10];
				if( strlen($telefone) == 11 && preg_match('/^091/', $telefone) ) {
					$telefone = substr($telefone, 1);
				} elseif( strlen($telefone) < 10 ) {
					$telefone = str_pad($telefone, 10, '0', STR_PAD_LEFT);
				} elseif( strlen($telefone) > 11 ) {
					if( preg_match('/^091/', $telefone) ) {
						$telefone = substr($telefone, 1, 10);
					} else {
						$telefone = substr($telefone, 0, 10);
					}
				}
				$this->data['Proprietario']['nome']		= $linha[3]  or '***PROPRIETÁRIO SEM NOME***';
				$this->data['Proprietario']['rg'] 		= $linha[25];
				$this->data['Proprietario']['cpf_cnpj']	= $cpfCnpj;
				$this->data['Proprietario']['endereco']	= 'ROD. ARTUR BERNARDES, 1650';
				$this->data['Proprietario']['bairro'] 	= 'PRATINHA I';
				$this->data['Proprietario']['cep'] 		= '66825-000';
				$this->data['Proprietario']['cidade'] 	= 'BELÉM';
				$this->data['Proprietario']['uf'] 		= 'PA';
				$this->data['Proprietario']['fone1'] 	= $telefone or '9132222222';
				$this->data['Proprietario']['fone2'] 	= $linha[10];

				$this->data		= $this->Unidade->Proprietario->create($this->data);
				$this->Unidade->Proprietario->save($this->data);
 				$this->out('> Inserting Proprietario "' . $linha[3] . '"...');

				$proprietarioId	= $this->Unidade->Proprietario->id;

			} else {
				$proprietarioId	= $proprietario['Proprietario']['id'];
			}
			$quadra_id	= $linha[0];
			$lote		= str_pad($linha[1], 2, '0', STR_PAD_LEFT);

			$this->Unidade->contain();
			$unidade	= $this->Unidade->find('first', array('fields'=> array('id')
				, 'conditions'=> array(
					  'and'=> array(
						  'Unidade.quadra_id'	=> $quadra_id
						, 'Unidade.lote'=> $lote
					)
				)
			));
			$this->Unidade->id			= $unidade['Unidade']['id'];
			$this->Unidade->saveField('morador_nome'	, strtoupper($linha[3]));
			$this->Unidade->saveField('morador_cpf'		, $linha[4]);
			$this->Unidade->saveField('morador_fone1'	, $linha[10]);
			$this->Unidade->saveField('morador_email'	, strtolower($linha[11]));
			$this->Unidade->saveField('morador_rg'		, $linha[24]);
			$this->Unidade->saveField('proprietario_id'	, $proprietarioId);
			$this->out('> Update morador Unidade "' . $this->Unidade->id . '"...');
		}
	}

	// crash something!
	function down() {
		$this->Proprietario	=& ClassRegistry::init('Proprietario');
		$this->Proprietario->query('DROP INDEX i_proprietarios_cpf_cnpj ON proprietarios');
		$this->Proprietario->deleteAll(true);
	}
}
