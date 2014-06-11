<?php
/*
 * AjustarArquivosRetorno Migration
 * 10/09/2009 18:16:57
 */
class AjustarArquivosRetorno extends AppMigration {

	// do something!
	function up() {

		$this->removeColumn('pagamentos', 'arquivo_retorno');
		$this->dropTable('arquivos_retorno');
		$this->createTable('arquivos_retorno', array(
			  'dta_gravacao'=> array('type'=> 'date', 'null'=> false)
			, 'arquivo'		=> array('type'=> 'string', 'null'=> false)
			, 'processado'	=> array('type'=> 'boolean', 'null'=> false, 'default'=> false)
		));
	}

	// crash something!
	function down() {
		$this->addColumn('pagamentos', 'arquivo_retorno', 'string');
	}
}
