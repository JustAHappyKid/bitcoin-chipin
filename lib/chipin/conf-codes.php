<?php

namespace Chipin\ConfCodes;

require_once 'spare-parts/database.php';
require_once 'chipin/users.php';

use \SpareParts\Database as DB, \Chipin\User, \Exception;

function isValidCode($code) {
  return DB\countRows('confirmation_codes', 'code = ? AND expires >= NOW()', array($code)) > 0;
}

function getUserForCode($code) {
  $rows = DB\simpleSelect('confirmation_codes', 'code = ?', array($code));
  $row = current($rows);
  if ($row == null) throw new Exception("No such confirmation code found");
  return User::loadFromID($row['user_id']);
}

function removeCode($code) {
  DB\delete('confirmation_codes', 'code = ?', array($code));
}
