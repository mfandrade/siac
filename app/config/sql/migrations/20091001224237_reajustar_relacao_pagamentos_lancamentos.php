<?php
/*
 * ReajustarRelacaoPagamentosLancamentos Migration
 * 10/01/2009 22:42:37
 */
App::import('Model', array('Lancamento', 'Pagamento'));
class ReajustarRelacaoPagamentosLancamentos extends AppMigration {

	// do something!
	function up() {

		$this->addColumn('pagamentos', 'lancamento_id');
		$this->removeColumn('lancamentos', 'pagamento_id');

		$this->addColumn('pagamentos', 'parcela', array('type'=> 'integer', 'null'=> false, 'default'=> 1));
		$this->addColumn('lancamentos', 'total_parcelas', array('type'=> 'integer', 'null'=> false, 'default'=> 1));

		$this->Pagamento	=& ClassRegistry::init('Pagamento');
		$this->Pagamento->query('CREATE INDEX i_pagamentos_lancamento_id USING BTREE ON pagamentos (lancamento_id)');
	}

	// crash something!
	function down() {

		$this->removeColumn('pagamentos', 'lancamento_id');
		$this->addColumn('lancamentos', 'pagamento_id');

		$this->removeColumn('pagamentos', 'parcela');
		$this->removeColumn('lancamentos', 'total_parcelas');

		$this->Lancamento	=& ClassRegistry::init('Lancamento');
		$this->Lancamento->query('CREATE INDEX i_lancamentos_pagamento_id USING BTREE ON lancamentos (pagamento_id)');
	}
}
