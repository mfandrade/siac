<?php
class SeleniumTestCase extends CakeWebTestCase {

	var $selenium		= null;

	function setUp() {
		App::import('Vendor', 'Testing_Selenium', array('file'=> 'selenium'.DS.'Selenium.php'));
		$this->selenium = new Testing_Selenium('*firefox', 'http://localhost/siac');
		$this->selenium->start();
	}

	function tearDown() {
		$this->selenium->stop();
		$this->selenium = null;
	}

}
