<?php
/*
 * TrocarTipoLote Migration
 * 10/14/2009 11:44:52
 */
App::import('model', 'Unidade');
class TrocarTipoLote extends AppMigration {

	// do domething!
	function up() {
		$this->Unidade	=& ClassRegistry::init('Unidade');
		$this->Unidade->query('ALTER TABLE unidades MODIFY COLUMN lote int(2) unsigned zerofill');
	}

	// crash something!
	function down() {
		$this->changeColumn('unidades', 'lote', array('type'=> 'string', 'length'=> 2));
	}
}

?>
