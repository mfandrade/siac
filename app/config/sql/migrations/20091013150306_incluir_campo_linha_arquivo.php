<?php
/*
 * IncluirCampoLinhaArquivo Migration
 * 10/13/2009 15:03:06
 */
class IncluirCampoLinhaArquivo extends AppMigration {
	// do something!
	function up() {
		$this->addColumn('pagamentos', 'linha_arquivo', array('type'=> 'integer'));
	}

	// crash something!
	function down() {
		$this->removeColumn('pagamentos', 'linha_arquivo');
	}
}
?>
