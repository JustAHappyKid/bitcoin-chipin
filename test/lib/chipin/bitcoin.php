<?php

require_once 'chipin/bitcoin.php';
use \Chipin\Bitcoin;

function testToBTCFunction() {
  $priceOfDollar = Bitcoin\toBTC('USD', 1);
  $f = floatval($priceOfDollar);
  assertTrue($f > 0);
  // assertEqual(floatval($priceOfDollar), floatval(strval($priceOfDollar)));
}
