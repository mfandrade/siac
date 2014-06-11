<?php
App::import('Model', 'TipoLancamento');
class CadastrarTiposLancamento extends AppMigration {

	function up() {

		$this->TipoLancamento	=& ClassRegistry::init('TipoLancamento');
		$this->TipoLancamento->query("INSERT INTO tipos_lancamento (cod, descricao, created, modified) VALUES (-1, 'TODOS', now(), now());");
		$this->TipoLancamento->query("INSERT INTO tipos_lancamento (cod, descricao, created, modified) VALUES ( 0, 'TAXA CONDOMINIAL', now(), now());");
		$this->TipoLancamento->query("INSERT INTO tipos_lancamento (cod, descricao, created, modified) VALUES ( 1, 'TAXA EXTRA', now(), now());");
		$this->TipoLancamento->query("INSERT INTO tipos_lancamento (cod, descricao, created, modified) VALUES ( 2, 'MULTA POR INFRAÇÃO', now(), now());");
		$this->TipoLancamento->query("INSERT INTO tipos_lancamento (cod, descricao, created, modified) VALUES ( 3, 'ACORDO', now(), now());");
	}

	function down() {

		$this->TipoLancamento	=& ClassRegistry::init('TipoLancamento');
		$this->TipoLancamento->deleteAll(true);
	}
}
