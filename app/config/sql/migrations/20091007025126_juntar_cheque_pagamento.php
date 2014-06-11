<?php
/*
 * JuntarChequePagamento Migration
 * 10/07/2009 02:51:26
 */
class JuntarChequePagamento extends AppMigration {
	// do something!
	function up() {
		$this->addColumn('pagamentos', 'cheque', array('type'=> 'float', 'length'=> '10,2', 'default'=> 0.00));
		$this->addColumn('pagamentos', 'cheque_dia', array('type'=> 'date'));
		$this->addColumn('pagamentos', 'cheque_banco_id', array('type'=> 'integer'));
		$this->addColumn('pagamentos', 'cheque_info', array('type'=> 'text'));
		$this->addColumn('pagamentos', 'compensacao', array('type'=> 'boolean', 'default'=> false));
		$this->addColumn('pagamentos', 'arquivo_retorno', array('type'=> 'text'));

		$this->removeColumn('pagamentos', 'forma_pagamento_id');
		$this->dropTable('formas_pagamento');
	}

	// crash something!
	function down() {
		$this->createTable('formas_pagamento', array(
			  'cheque'=> array('type'=> 'float', 'length'=> '10,2', 'default'=> 0.00)
			, 'cheque_dia'=> array('type'=> 'date')
			, 'cheque_banco_id'=> array('type'=> 'integer')
			, 'cheque_info'=> array('type'=> 'text')
		));
		$this->addColumn('pagamentos', 'formas_pagamento_id', array('type'=> 'integer'));
		$this->removeColumn('pagamentos', 'arquivo_retorno');
		$this->removeColumn('pagamentos', 'compensacao');
		$this->removeColumn('pagamentos', 'cheque_info');
		$this->removeColumn('pagamentos', 'cheque_banco_id');
		$this->removeColumn('pagamentos', 'cheque_dia');
		$this->removeColumn('pagamentos', 'cheque');
	}
}
