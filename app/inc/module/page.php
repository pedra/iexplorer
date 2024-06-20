<?php

namespace Module;

class Page {

	private $path = '';

	public function __construct() {
		$this->path = PATH_ROOT . '/page/';
	}

	public function home($params, $queries) {
		include_once $this->path . 'home.html';
		exit();
	}
}