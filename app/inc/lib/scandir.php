<?php

namespace Lib;

class Scandir {

	private $dir = null;

	public function __construct($dir = '') {
		$this->dir = $dir;
	}

	public function scan() {
		$dd = [];
		$df = [];
		$s = scandir($this->dir);
		foreach ($s as $d) {
			if (in_array($d, IGNORE))
				continue;

			if (is_dir($this->dir . '/' . $d)) {
				array_push($dd, $d);
			} else {
				array_push($df, [
					"name" => $d,
					"size" => human_filesize(filesize($this->dir . '/' . $d)),
					"ext" => strtolower(pathinfo($this->dir . '/' . $d)['extension'] ?? '')
				]);
			}
		}

		header('Content-Type: application/json');
		exit(json_encode([
			"dir" => $dd,
			"file" => $df
		]));
	}
}