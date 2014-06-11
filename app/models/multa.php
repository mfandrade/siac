<?php
class Multa extends AppModel {
	var $name			= 'Multa';
	var $displayField	= 'motivo';
	var $hasMany		= array('Lancamento');
}