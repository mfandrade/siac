<?php
class Configuracao extends AppModel {
	var $name		= 'Configuracao';
	var $validates	= array(
		  'perfil'	=> array('rule'=> 'isUnique')
	);
}
