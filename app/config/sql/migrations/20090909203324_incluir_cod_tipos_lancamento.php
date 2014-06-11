<?php
App::import('Model', 'TipoLancamento');
class IncluirCodTiposLancamento extends AppMigration {

	function up() {
		$this->addColumn('tipos_lancamento', 'cod', array('type'=> 'integer', 'null'=> false));

		$this->TipoLancamento	=& ClassRegistry::init('TipoLancamento');
		$this->TipoLancamento->query('CREATE UNIQUE INDEX i_tipos_lancamento_cod USING BTREE ON tipos_lancamento(cod)');
	}

	function down() {

		$this->TipoLancamento	=& ClassRegistry::init('TipoLancamento');
		$this->TipoLancamento->query('DROP INDEX i_tipos_lancamento_cod ON tipos_lancamento');

		$this->removeColumn('tipos_lancamento', 'cod');
	}
}
