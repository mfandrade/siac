<?php
/*
id do projeto
- siac

método de sincronizacao
- direct copy
- version control (cvs, svn, git, mercurial, bazaar)
	- svn
		- url repositório
		- usuário/senha
		- método: checkout/export
		- pasta
		- tag/revision (HEAD)
- ftp
	- host
	- usuário
	- senha
	- pasta

- rsync
	- ???


modo de instalação
	- desenvolvimento: pergunta o caminho para www e copia tudo para uma pasta chamada "projeto"
	- avancado: pergunta o caminho para www, o caminho para app e o caminho para cake, copia o webroot para uma subpasta em www e modifica o index, bootstrap etc de acordo

config. ambiente remoto
	- servidor web
		- reescrita de url?
	- banco de dados
		default	- usuário/senha
		test	- usuário/senha

proceed
*/ 

/**
 * Deploy shell for CakePHP.
 *
 * PHP versions 4 and 5
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2009-2010, Marcelo F Andrade. (http://ocakeeisso.wordpress.com)
 * @link          http://bakery.cakephp.org
 * @package       cake
 * @subpackage    cake.vendors.shells
 * @since         CakePHP(tm) v 1.2.5
 * @version       $Revision: 0.1 $
 * @modifiedby    $LastChangedBy: mfandrade $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Bake is a command-line code generation utility for automating programmer chores.
 *
 * @package       cake
 * @subpackage    cake.vendors.shells
 * @link          http://bakery.cakephp.org
 */
class DeployShell extends Shell {
/**
 * Contains tasks to load and instantiate
 *
 * @var array
 * @access public
 */
	var $tasks = array('Project', 'DbConfig', 'Model', 'Controller', 'View', 'Plugin', 'Test');
/**
 * Override loadTasks() to handle paths
 *
 * @access public
 */
	function loadTasks() {
		parent::loadTasks();
		$task = Inflector::classify($this->command);
		if (isset($this->{$task}) && !in_array($task, array('Project', 'DbConfig'))) {
			$path = Inflector::underscore(Inflector::pluralize($this->command));
			$this->{$task}->path = $this->params['working'] . DS . $path . DS;
			if (!is_dir($this->{$task}->path)) {
				$this->err(sprintf(__("%s directory could not be found.\nBe sure you have created %s", true), $task, $this->{$task}->path));
				$this->_stop();
			}
		}
	}
/**
 * Override main() to handle action
 *
 * @access public
 */
	function main() {
		if (!is_dir($this->DbConfig->path)) {
			if ($this->Project->execute()) {
				$this->DbConfig->path = $this->params['working'] . DS . 'config' . DS;
			}
		}

		if (!config('database')) {
			$this->out(__("Your database configuration was not found. Take a moment to create one.", true));
			$this->args = null;
			return $this->DbConfig->execute();
		}
		$this->out('CakePHP\'s Deploy Shell');
		$this->hr();
		$this->out('1) Define ID for this app');
		$this->out('2) Define the file transfer method');
		$this->out('3) Configure remote environment');
		$this->out('4) Deploy this application now');
		$this->out('Q) Quit');

		$classToBake = strtoupper($this->in(__('What would you like to do next?', true), array('1', '2', '3', '4', 'Q')));
		switch ($classToBake) {
			case 'D':
				$this->DbConfig->execute();
				break;
			case 'M':
				$this->Model->execute();
				break;
			case 'V':
				$this->View->execute();
				break;
			case 'C':
				$this->Controller->execute();
				break;
			case 'P':
				$this->Project->execute();
				break;
			case 'Q':
				exit(0);
				break;
			default:
				$this->out(__('You have made an invalid selection. Please choose a type of class to Bake by entering D, M, V, or C.', true));
		}
		$this->hr();
		$this->main();
	}
/**
 * Quickly bake the MVC
 *
 * @access public
 */
	function all() {
		$ds = 'default';
		$this->hr();
		$this->out('Bake All');
		$this->hr();

		if (isset($this->params['connection'])) {
			$ds = $this->params['connection'];
		}

		if (empty($this->args)) {
			$name = $this->Model->getName($ds);
		}

		if (!empty($this->args[0])) {
			$name = $this->args[0];
			$this->Model->listAll($ds, false);
		}

		$modelExists = false;
		$model = $this->_modelName($name);
		if (App::import('Model', $model)) {
			$object = new $model();
			$modelExists = true;
		} else {
			App::import('Model');
			$object = new Model(array('name' => $name, 'ds' => $ds));
		}

		$modelBaked = $this->Model->bake($object, false);

		if ($modelBaked && $modelExists === false) {
			$this->out(sprintf(__('%s Model was baked.', true), $model));
			if ($this->_checkUnitTest()) {
				$this->Model->bakeTest($model);
			}
			$modelExists = true;
		}

		if ($modelExists === true) {
			$controller = $this->_controllerName($name);
			if ($this->Controller->bake($controller, $this->Controller->bakeActions($controller))) {
				$this->out(sprintf(__('%s Controller was baked.', true), $name));
				if ($this->_checkUnitTest()) {
					$this->Controller->bakeTest($controller);
				}
			}
			if (App::import('Controller', $controller)) {
				$this->View->args = array($controller);
				$this->View->execute();
			}
			$this->out(__('Bake All complete'));
			array_shift($this->args);
		} else {
			$this->err(__('Bake All could not continue without a valid model', true));
		}

		if (empty($this->args)) {
			$this->all();
		}
		$this->_stop();
	}

/**
 * Displays help contents
 *
 * @access public
 */
	function help() {
		$this->out('CakePHP Bake:');
		$this->hr();
		$this->out('The Bake script generates controllers, views and models for your application.');
		$this->out('If run with no command line arguments, Bake guides the user through the class');
		$this->out('creation process. You can customize the generation process by telling Bake');
		$this->out('where different parts of your application are using command line arguments.');
		$this->hr();
		$this->out("Usage: cake bake <command> <arg1> <arg2>...");
		$this->hr();
		$this->out('Params:');
		$this->out("\t-app <path> Absolute/Relative path to your app folder.\n");
		$this->out('Commands:');
		$this->out("\n\tbake help\n\t\tshows this help message.");
		$this->out("\n\tbake all <name>\n\t\tbakes complete MVC. optional <name> of a Model");
		$this->out("\n\tbake project <path>\n\t\tbakes a new app folder in the path supplied\n\t\tor in current directory if no path is specified");
		$this->out("\n\tbake plugin <name>\n\t\tbakes a new plugin folder in the path supplied\n\t\tor in current directory if no path is specified.");
		$this->out("\n\tbake db_config\n\t\tbakes a database.php file in config directory.");
		$this->out("\n\tbake model\n\t\tbakes a model. run 'bake model help' for more info");
		$this->out("\n\tbake view\n\t\tbakes views. run 'bake view help' for more info");
		$this->out("\n\tbake controller\n\t\tbakes a controller. run 'bake controller help' for more info");
		$this->out("");

	}
}
?>
