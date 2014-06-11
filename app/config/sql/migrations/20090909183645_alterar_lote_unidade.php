<?php
App::import('Model', 'Unidade');
class AlterarLoteUnidade extends AppMigration {

	function up() {
		$this->Unidade	=& ClassRegistry::init('Unidade');
		$this->Unidade->query('ALTER TABLE unidades MODIFY lote INT(2) ZEROFILL NOT NULL');
	}

	function down() {
	}
}
