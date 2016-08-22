<?php
class MissingParameterException extends Exception {
	public function __construct() {
		parent::__construct("Missing parameters");
	}
}
?>
