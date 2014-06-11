<?php
class Quadra extends AppModel {
	var $name			= 'Quadra';
	var $displayField	= 'descricao';
	var $hasMany		= array('Unidade');
}
