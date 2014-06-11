<?php
App::import('Model', 'Quadra');
class CadastrarQuadras extends AppMigration {
	
    function up() {
			
		for( $i= 1; $i <= 18; $i++ ) {
			switch( $i ) {
				case 1:	$totalLotes	= 36;	break;
				case 2:	$totalLotes	= 20;	break;
				case 3:	$totalLotes	= 34;	break;
				case 4:	$totalLotes	= 18;	break;
				
				case 5:	$totalLotes	= 36;	break;
				case 6:	$totalLotes	= 20;	break;
				case 7:	$totalLotes	= 32;	break;
				case 8:	$totalLotes	= 16;	break;
				
				case 9:	$totalLotes	= 34;	break;
				case 10:$totalLotes	= 18;	break;
				case 11:$totalLotes	= 34;	break;
				case 12:$totalLotes	= 18;	break;
				
				case 13:$totalLotes	= 36;	break;
				case 14:$totalLotes	= 20;	break;
				case 15:$totalLotes	= 34;	break;
				case 16:$totalLotes	= 18;	break;
				
				case 17:$totalLotes	= 18;	break;
				case 18:$totalLotes	= 10;	break;
				
				case 99:$totalLotes	= 20;	break;
			}
			$this->data[]	= array(
				'Quadra'	=> array(
					  'abbr'		=> 'Q'.sprintf('%02s', $i)
					, 'descricao'	=> 'QUADRA '.sprintf('%02s', $i)
					, 'total_lotes'	=> $totalLotes
				)
			);
		}
		$this->data[]	= array(
			'Quadra'	=> array(
				  'id'			=> 99
				, 'abbr'		=> 'COM'
				, 'descricao'	=> 'QUADRA COMERCIAL'
				, 'total_lotes'	=> 20
			)
		);

		$this->Quadra	=& ClassRegistry::init('Quadra');
		$this->data		= $this->Quadra->create($this->data);
		$this->Quadra->saveAll($this->data);
    }
	
    function down() {
		$this->Quadra	=& ClassRegistry::init('Quadra');
		$this->Quadra->deleteAll('1=1');
		$this->Quadra->query('ALTER TABLE quadras AUTO_INCREMENT 0');
    }
}

