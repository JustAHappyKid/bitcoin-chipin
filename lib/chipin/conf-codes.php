<?php

namespace Chipin\ConfCodes;

require_once 'my-php-libs/database.php';
use \MyPHPLibs\Database as DB;

function isValidCode($code) {
  return DB\countRows('confirmation_codes', 'code = ? AND expires >= NOW()', array($code)) > 0;
}

function removeCode($code) {
  DB\delete('confirmation_codes', 'code = ?', array($code));
}
