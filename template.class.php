<?php

/**
 * Template class
 *
 * @copyright	http://www.mekdrop.name
 * @license		http://www.opensource.org/licenses/lgpl-3.0.html
 * @package     main
 * @since       0.1
 * @author		MekDrop <github@mekdrop.name>
 * @version     $Id$
 */

class TemplateEngine {

	var $vars = array();

	function assign($var_name, $var_content) {
		$this->vars[$var_name] = $var_content;
	}

	function logicAssign($var_name, $logic, $var_content) {
		$this->assign($var_name, $logic?$var_content:'');
	}

	function render($filename) {
		$data = file_get_contents($filename);
		foreach ($this->vars as $var => $value) {
			$data = str_replace( '#' . $var .'#', $value, $data );
		}
		echo $data;
	}

}

$objTemplate = new TemplateEngine();

?>