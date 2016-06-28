<?php

namespace Test;

use PHPUnit_Framework_BaseTestListener;
use PHPUnit_Framework_TestSuite;

class Bootstrap extends PHPUnit_Framework_BaseTestListener {

	/**
     * @param PHPUnit_Framework_TestSuite $suite
     */
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite) {
		$phinxApp = new \Phinx\Console\PhinxApplication();
		$phinxTextWrapper = new \Phinx\Wrapper\TextWrapper($phinxApp);
		$phinxTextWrapper->setOption('configuration', 'phinx.yml');
		$phinxTextWrapper->setOption('parser', 'YAML');
		$phinxTextWrapper->setOption('environment', 'development');
		$phinxTextWrapper->getMigrate();
		$phinxTextWrapper->getSeed();
    }

 	public function endTestSuite(PHPUnit_Framework_TestSuite $suite) {
 		$phinxApp = new \Phinx\Console\PhinxApplication();
		$phinxTextWrapper = new \Phinx\Wrapper\TextWrapper($phinxApp);
		$phinxTextWrapper->setOption('configuration', 'phinx.yml');
		$phinxTextWrapper->setOption('parser', 'YAML');
		$phinxTextWrapper->setOption('environment', 'development');
		$phinxTextWrapper->getRollback();
  	}
}
