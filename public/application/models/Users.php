<?php

require_once 'chipin/users.php';
require_once 'my-php-libs/database.php';

use \MyPHPLibs\Database as DB;

class Application_Model_Users {

  public function sendLinkForChangingPassword($email) {
    try {
      $user = User::loadFromEmailAddr($email);
      $confCode = $this->generateConfCode(10);
      DB\insertOne('confirmation_codes',
        array('user_id' => $user->id, 'code' => $confCode, 'created_at' => date("Y-m-d H:i:s"),
              'expires' => date("Y-m-d H:i:s", strtotime("+3 days"))));
      $this->sendPassResetEmail($email, $confCode);
      return true;
    } catch (NoSuchUser $_) {
      return false;
    }
  }

  private function sendPassResetEmail($email, $confCode) {
    $url = PATH . 'signin/approve/?code=' . $confCode;
    //logMsg('debug', 'Sending pass-reset email with link ' . $url);
    $mail = new Zend_Mail();
    $mail->setFrom('webmaster@bitcoinchipin.com');
    $mail->addTo($email, 'recipient');
    $mail->setSubject('BitcoinChipin.com Password Reset');
    $mail->setBodyText(
      "Please use the following link to reset your password:\n\n" . $url);
    $mail->send();
  }

  private function generateConfCode($length = 8) {
    $confCode = "";
    $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

    $maxlength = strlen($possible);
    if ($length > $maxlength) $length = $maxlength;

    $i = 0;
    while ($i < $length) {
      $char = substr($possible, mt_rand(0, $maxlength-1), 1);
      if (!strstr($confCode, $char)) {
        $confCode .= $char;
        $i++;
      }
    }
    return $confCode;
  }

  /*
  public function checkUser($user_id, $code) {
    $select = $this->_dbTable->select()
      ->from('reset_password')
      ->where(array('user_id = "' . $user_id . '" AND ' .
        'DATE(expires) BETWEEN DATE_ADD(DATE(NOW()), INTERVAL -1 DAY)) AND DATE(NOW())'));
    $result = $this->_dbTable->fetchAll($select);
    return $result;
  }
  */

  public function saveUserLoginInformation() {
    $userAgent = new Zend_Http_UserAgent();
    $device = $userAgent->getDevice();
    qaq($device);
    $browser = $device->getBrowser();
  }
}
