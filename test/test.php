#!/usr/bin/php -dsendmail_path=mock-sendmail
<?php

use \SpareParts\Test, \SpareParts\Database as DB, \Chipin\User, \Chipin\Passwords,
  \Chipin\Widgets, \Chipin\Widgets\Widget, \Chipin\Bitcoin;

function main($argc, $argv) {
  $testDir = realpath(dirname(__FILE__));
  $libsDir = realpath(dirname($testDir) . '/lib');
  set_include_path($libsDir . PATH_SEPARATOR . get_include_path());

  set_include_path(dirname(__FILE__) . '/mock/lib' . PATH_SEPARATOR . get_include_path());

  error_reporting(E_ALL);

  require_once 'chipin/env/log.php';
//  \Chipin\Log\configure();
  require_once 'spare-parts/database.php';
  DB\setConnectionParams($driver = 'mysql', $dbName = 'chipin_test',
    $username = 'chipin_test', $password = 'password', $host = 'localhost');
  require_once 'spare-parts/test/mock-sendmail.php';
  Test\MockEmail\addMockSendmailToPath(dirname(__FILE__) . '/mock/bin/mock-sendmail');
  require_once 'spare-parts/test/base-framework.php';
  $filesToIgnore = array('test.php', 'config.ini', 'mock/bin/mock-sendmail',
    'lib/chipin/env/conf1.ini', 'lib/chipin/env/conf2.ini');
  Test\testScriptMain($testDir, $filesToIgnore, $argc, $argv);
}

function clearDB() {
  DB\delete('widgets', 'TRUE', array());
  DB\delete('confirmation_codes', 'TRUE', array());
  DB\delete('subscriptions', 'TRUE', array());
  DB\delete('users', 'TRUE', array());
}

function getUser($email = 'joe@test.net') {
  if (DB\countRows('users', "email = ?", array($email)) == 0) {
    return newUser($email, 'big-joe-' . time(), 'something');
  } else {
    return User::loadFromEmailAddr($email);
  }
}

function newUser($email, $username, $pw) {
  require_once 'chipin/users.php';
  require_once 'chipin/passwords.php';
  # XXX: Note, we're not hashing passwords here...
  $uid = DB\insertOne('users',
    array('email' => $email, 'username' => $username, 'password' => Passwords\hash($pw)), true);
  return User::loadFromID($uid);
}

function getWidget(User $u = null) {
  require_once 'chipin/widgets.php';
  if ($u == null) $u = getUser();
  $w = new Widget;
  $w->ownerID = $u->id;
  $w->title = "Test Widget";
  $w->ending = new DateTime('2020-06-30');
  $w->setGoal(100, 'USD');
  $w->setDimensions(350, 310);
  $colors = Widgets\allowedColors();
  $w->color = $colors[0];
  $w->bitcoinAddress = getBitcoinAddr();
  $w->countryCode = 'CA';
  $w->about = "This is a test widget!";
  $w->save();
  return $w;
}

function getBitcoinAddr($btcBalance = null) {
  require_once 'chipin/bitcoin.php';
  $a = '15Mux55YKsWp9pe5eUC2jcP5R9K7XA4pPF';
  if ($btcBalance !== null) {
    DB\delete('bitcoin_addresses', 'address = ?', array($a));
    DB\insertOne('bitcoin_addresses',
      array('address' => $a, 'satoshis' => $btcBalance * Bitcoin\satoshisPerBTC(),
            'updated_at' => new DateTime('now')));
  }
  return $a;
}

main($argc, $argv);
