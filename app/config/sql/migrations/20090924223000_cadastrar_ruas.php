<?php
App::import('Model', 'Rua');
class CadastrarRuas extends AppMigration {

    function up() {

		$this->data[] = array('Rua'=> array('id'=> 'BRA', 'ordem'=> 1, 'descricao'=> 'BRASIL'));
		$this->data[] = array('Rua'=> array('id'=> 'ARG', 'ordem'=> 2, 'descricao'=> 'ARGENTINA'));
		$this->data[] = array('Rua'=> array('id'=> 'BOL', 'ordem'=> 3, 'descricao'=> 'BOLÍVIA'));
		$this->data[] = array('Rua'=> array('id'=> 'COL', 'ordem'=> 4, 'descricao'=> 'COLÔMBIA'));
		$this->data[] = array('Rua'=> array('id'=> 'CHI', 'ordem'=> 5, 'descricao'=> 'CHILE'));
		$this->data[] = array('Rua'=> array('id'=> 'ECU', 'ordem'=> 6, 'descricao'=> 'EQUADOR'));
		$this->data[] = array('Rua'=> array('id'=> 'PER', 'ordem'=> 7, 'descricao'=> 'PERU'));
		$this->data[] = array('Rua'=> array('id'=> 'PAR', 'ordem'=> 8, 'descricao'=> 'PARAGUAI'));
		$this->data[] = array('Rua'=> array('id'=> 'URU', 'ordem'=> 9, 'descricao'=> 'URUGUAI'));
		$this->data[] = array('Rua'=> array('id'=> 'VEN', 'ordem'=> 10, 'descricao'=> 'VENEZUELA'));

		$this->Rua	=& ClassRegistry::init('Rua');
		$this->Rua->contain();
		$this->Rua->saveAll($this->data);
    }

    function down() {
		$this->Rua	=& ClassRegistry::init('Rua');
		$this->Rua->contain();
		$this->Rua->deleteAll('1=1');
    }
}

