<?php
class Rua extends AppModel {
	var $name			= 'Rua';
	var $displayField	= 'descricao';
	var $hasMany		= array('Unidade');
}