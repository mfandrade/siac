<?php
/*
 * InserirUsuario Migration
 * 10/01/2009 22:51:07
 */
App::import('Model', 'Usuario');
class InserirUsuario extends AppMigration {

	// do something!
	function up() {
		$this->Usuario	=& ClassRegistry::init('Usuario');
		$this->Usuario->query('CREATE UNIQUE INDEX i_usuarios_usuario USING BTREE ON usuarios(usuario)');

		$count	= $this->Usuario->find('count', array('conditions'=> array('Usuario.usuario'=> 'admin')));
		if( $count == 0 ) {

			$this->data['Usuario']['usuario']		= 'admin';
			$this->data['Usuario']['senha']			= '76d838d0804b6abd553b1304b96dda29888a26ed';
			$this->data['Usuario']['nome_completo']	= 'ADMINISTRADOR';
			$this->Usuario->save($this->data);
		}
	}

	// crash something!
	function down() {
		$this->Usuario	=& ClassRegistry::init('Usuario');
		$this->Usuario->deleteAll(true);
		$this->Usuario->query('DROP INDEX i_usuarios_usuario ON usuarios');
	}
}
