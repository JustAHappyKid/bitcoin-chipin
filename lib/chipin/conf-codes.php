<?php

namespace Chipin\ConfCodes;

require_once 'spare-parts/database.php';
require_once 'chipin/users.php';

use \SpareParts\Database as DB, \Chipin\User, \Exception;

function generateAndSave(User $user) {
  $confCode = generate(10);
  DB\insertOne('confirmation_codes',
    array('user_id' => $user->id, 'code' => $confCode, 'created_at' => date("Y-m-d H:i:s"),
          'expires' => date("Y-m-d H:i:s", strtotime("+3 days"))));
}

function isValidCode($code) {
  return DB\countRows('confirmation_codes', 'code = ? AND expires >= NOW()', array($code)) > 0;
}

function getUserForCode($code) {
  $rows = DB\simpleSelect('confirmation_codes', 'code = ?', array($code));
  $row = current($rows);
  if ($row == null) throw new InvalidCode("No such confirmation code found ($code)");
  return User::loadFromID($row['user_id']);
}

function removeCode($code) {
  DB\delete('confirmation_codes', 'code = ?', array($code));
}

function generate($length, $chars = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ") {
  $confCode = "";
  if ($length > strlen($chars))
    throw new \InvalidArgumentException("\$length cannot be longer than \$chars characters");
  $i = 0;
  while ($i < $length) {
    $char = substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    if (!strstr($confCode, $char)) {
      $confCode .= $char;
      $i++;
    }
  }
  return $confCode;
}

class InvalidCode extends Exception {}
