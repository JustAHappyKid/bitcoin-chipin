<?php

require_once 'my-php-libs/database.php';
use \MyPHPLibs\Database as DB;

class Application_Model_Register {

  public function createUser($data) {
    DB\insertOne('users', $data);
  }

  public function updateUsersPassword($password, $uid) {
    // $this->_dbTable->update('users', $data, $id);
    DB\query('UPDATE users SET password = ? WHERE id = ?', array($password, $uid));
  }

  public function isValidConfirmation($code, $userID) {
      /*
    $select = $this->_dbTable
      ->select()
      ->from('reset_password')
      ->where('user_id = "'.$userID.'" AND code="'.$code.'" AND expires >= NOW() AND status = 1')
      ->order('id DESC')
      ->limit('1');
    $result = $this->_dbTable->fetchAll($select);
    return isset($result[0]);
      */
    return DB\countRows('confirmation_codes', 'code = ? AND expires >= NOW()', array($code)) > 0;
  }

  public function removeConfirmationLink($code, $userID) {
    /*
    $this->_dbTable->delete('reset_password', array(
      'user_id = ?' => $userID,
      'code = ?' => $code
    ));
    */
    DB\delete('confirmation_codes', 'code = ?', array($code));
  }
}
