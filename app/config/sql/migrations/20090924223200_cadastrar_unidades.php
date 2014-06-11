<?php
App::import('Model', 'Rua');
App::import('Model', 'Quadra');
App::import('Model', 'Unidade');
class CadastrarUnidades extends AppMigration {

    function up() {

		$this->Rua		=& ClassRegistry::init('Rua');
		$this->Quadra	=& ClassRegistry::init('Quadra');

		$this->Rua->contain();
		$ruas		= $this->Rua->find('list', array('fields'=> array('id')));
		foreach( $ruas as $rua ) {

			switch( $rua ) {
				case 'BRA': $quadras= array(99=> -1); break;
				case 'ARG': $quadras= array(1=> 1,	2=> 1); break;
				case 'BOL': $quadras= array(1=> 0,	2=> 0,	3=> 1,	4=> 1); break;
				case 'COL': $quadras= array(3=> 0,	4=> 0,	5=> 1,	6=> 1); break;
				case 'CHI': $quadras= array(5=> 0,	6=> 0,	7=> 1,	8=> 1); break;
				case 'ECU': $quadras= array(7=> 0,	8=> 0,	9=> 1,	10=> 1); break;
				case 'PER': $quadras= array(9=> 0,	10=> 0,	11=> 1,	12=> 1); break;
				case 'PAR': $quadras= array(11=> 0,	12=> 0,	13=> 1,	14=> 1); break;
				case 'URU': $quadras= array(13=> 0,	14=> 0,	15=> 1,	16=> 1); break;
				case 'VEN': $quadras= array(15=> 0,	16=> 0,	17=> -1,18=> -1); break;
				default:
			}
			
			$this->Quadra->contain();
			foreach( $quadras as $id => $par ) {
				
				$quadra		= $this->Quadra->find('first', array('fields'=> array('id', 'total_lotes'), 'conditions'=> array('id'=> $id)));

				if( $par === 1 ) {
					$pri	= 1;
					$ult	= $quadra['Quadra']['total_lotes'] -1;
					$pass	= 2;
				} elseif( $par === 0 ) {
					$pri	= 2;
					$ult	= $quadra['Quadra']['total_lotes'];
					$pass	= 2;
				} else {
					$pri	= 1;
					$ult	= $quadra['Quadra']['total_lotes'];
					$pass	= 1;
				}

				//$this->out('> Inserting Q' . sprintf('%02s', $quadra['Quadra']['id']) . '... (' . $pri . ' to ' . $ult. ' step ' . $pass .')  - '.$rua);
				for( $l= $pri; $l <= $ult; $l+= $pass ) {

					$this->data[]	= array(
						'Unidade'	=> array(
							  'quadra_id'		=> $quadra['Quadra']['id']
							, 'rua_id'			=> $rua
							, 'lote'			=> $l
							, 'proprietario_id'	=> 1
							, 'morador_nome'	=> 'SEM MORADOR DEFINIDO'
					));
				}
			}
		}
		$this->Unidade	=& ClassRegistry::init('Unidade');
		$this->Unidade->saveAll($this->data, array('validate'=> 'first'));
    }

    function down() {
		$this->Unidade	=& ClassRegistry::init('Unidade');
		$this->Unidade->deleteAll(true);
		$this->Unidade->query('ALTER TABLE unidades AUTO_INCREMENT 0');
    }
}

