<?php
class Firebug_Widget extends CSX_SingletonWidget {
	protected function init() {
		parent::init();

		$this->application->AddHeadScript($this->getPubUrl() . '/firebug.js');
		$this->application->AddHeadStylesheet($this->getPubUrl() . '/firebug.css');
	}
}