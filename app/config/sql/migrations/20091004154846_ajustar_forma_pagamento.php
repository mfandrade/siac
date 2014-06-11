<?php
/*
 * AjustarFormaPagamento Migration
 * 10/04/2009 15:48:46
 */
//App::import('Model', 'FormaPagamento');
class AjustarFormaPagamento extends AppMigration {

	// do something!
	function up() {
		$this->removeColumn('formas_pagamento', 'descricao');
		$this->addColumn('formas_pagamento', 'dinheiro', array('type'=> 'float', 'length'=> '10,2', 'null'=> false, 'default'=> '0.00'));
		$this->addColumn('formas_pagamento', 'cheque', array('type'=> 'float', 'length'=> '10,2', 'null'=> false, 'default'=> '0.00'));
		$this->addColumn('formas_pagamento', 'cheque_banco_id', array('type'=> 'integer'));
		$this->addColumn('formas_pagamento', 'cheque_dia', array('type'=> 'date'));
		$this->addColumn('formas_pagamento', 'cheque_info', array('type'=> 'text'));
		$this->dropTable('cheques');
		//$this->FormaPagamento	=& ClassRegistry::init('FormaPagamento');
		//$this->FormaPagamento->query('CREATE INDEX i_cheque_banco_id USING BTREE ON formas_pagamento(cheque_banco_id)');
	}

	// crash something!
	function down() {
		//$this->FormaPagamento	=& ClassRegistry::init('FormaPagamento');
		//$this->FormaPagamento->query('DROP INDEX i_cheque_banco_id formas_pagamento');
		$this->removeColumn('formas_pagamento', 'cheque_info');
		$this->removeColumn('formas_pagamento', 'cheque_dia');
		$this->removeColumn('formas_pagamento', 'cheque_banco_id');
		$this->removeColumn('formas_pagamento', 'cheque');
		$this->removeColumn('formas_pagamento', 'dinheiro');
		$this->addColumn('formas_pagamento', 'descricao', array('type'=> 'string', 'length'=> 255, 'null'=> false));
	}
}
