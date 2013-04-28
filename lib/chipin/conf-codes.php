<?php

namespace Chipin\ConfCodes;

public function isValidCode($code) {
  return DB\countRows('confirmation_codes', 'code = ? AND expires >= NOW()', array($code)) > 0;
}

public function removeCode($code) {
  DB\delete('confirmation_codes', 'code = ?', array($code));
}
