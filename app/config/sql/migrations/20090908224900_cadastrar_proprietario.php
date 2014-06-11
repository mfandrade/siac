<?php
App::import('Model', 'Proprietario');
class CadastrarProprietario extends AppMigration {
	
    function up() {
		$this->Proprietario	=& ClassRegistry::init('Proprietario');
		$this->data		= array(
			'Proprietario'	=> array(
				  'id'			=> 1
				, 'cpf_cnpj'=> '11111111111'
				, 'nome'	=> '***PROPRIETÁRIO NÃO CADASTRADO***'
				, 'rg' 		=> '000000'
				, 'endereco'=> 'ROD. ARTUR BERNARDES, 1650'
				, 'bairro' 	=> 'PRATINHA I'
				, 'cep' 	=> '66825-000'
				, 'cidade' 	=> 'BELÉM'
				, 'uf' 		=> 'PA'
				, 'fone1' 	=> '9132222222'
		));
		$this->data	= $this->Proprietario->create($this->data);
		$this->Proprietario->save($this->data);
    }

    function down() {
		$this->Proprietario	=& ClassRegistry::init('Proprietario');
		$this->Proprietario->deleteAll('1=1');
		$this->Proprietario->query('ALTER TABLE proprietarios AUTO_INCREMENT 0');
    }
}
