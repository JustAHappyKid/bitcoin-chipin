<?php

require_once 'chipin/env/init.php';
use \Chipin\Environment as Env;

function testReadConfig() {
  $dir = dirname(__FILE__);
  $values = Env\readConfig(array("$dir/conf1.ini", "$dir/conf2.ini"));
  assertEqual("hello", $values['this.that']);
  assertEqual("goodbye", $values['something.else']);
  assertEqual("overridden-value", $values['overridden']);
}
