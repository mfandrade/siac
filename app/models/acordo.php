<?php
class Acordo extends AppModel {
	var $name			= 'Acordo';
	var $hasMany		= array('Lancamento');
}