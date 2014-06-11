<?php
App::import('Model', 'InstrucaoBoleto');
class CadastrarInstrucaoBoleto extends AppMigration {

	function up() {

		$this->InstrucaoBoleto	=& ClassRegistry::init('InstrucaoBoleto');
		$this->data				= array(
			  'InstrucaoBoleto'	=> array(
				  'texto'		=> "Sr. Caixa, até o vencimento, conceder\ndesconto de R$ 50,00 sobre os R$ 280,00;\nconforme Assembleia do dia 06/03/2008."
				, 'descricao'	=> 'Mensagem padrão'
		));
		$this->InstrucaoBoleto->save($this->data);
	}

	function down() {

		$this->InstrucaoBoleto	=& ClassRegistry::init('InstrucaoBoleto');
		$this->InstrucaoBoleto->deleteAll('1=1');

	}
}

?>
