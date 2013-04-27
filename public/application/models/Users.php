<?php

class Application_Model_Users {
  private $_dbTable;

  public function __construct() {
    $this->_dbTable = Zend_Db_Table::getDefaultAdapter();
  }

  public function sendLinkForChangingPassword($email) {
    $select = $this->_dbTable->select()->from('users')->where('email = "'.$email.'"');
    $user = $this->_dbTable->fetchAll($select);
    if (empty($user[0])){
      return false;
    } else {
      $result = $this->generateLink(8, $user[0]['id']);

      $this->_dbTable->insert('reset_password', array(
        'user_id' => $user[0]['id'],
        'link' => $result['url'],
        'expires' => date("Y-m-d H:i:s", strtotime("tomorrow")),
        'code' => $result['code'],
        'status' => 1
      ));

      $mail = new Zend_Mail();
      $mail->setFrom('webmaster@bitcoinchipin.com');
      $mail->addTo($email, 'recipient');
      $mail->setSubject('BitcoinChipin.com Password Reset');
      $mail->setBodyText(
        "Please use the following link to reset your password:\n\n" . $result['url']);
      $mail->send();
      return true;
    }
  }

  private function generateLink($length = 8, $user_id) {
    $password = "";
    $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

    $maxlength = strlen($possible);
    if ($length > $maxlength) $length = $maxlength;

    $i = 0;
    while ($i < $length) {
      $char = substr($possible, mt_rand(0, $maxlength-1), 1);
      if (!strstr($password, $char)) {
        $password .= $char;
        $i++;
      }
    }
    return array(
      'url' => PATH . 'signin/approve/?code=' . $password . '&user_id=' . $user_id,
      'code' => $password
    );
  }

  public function checkUser($user_id, $code) {
    $select = $this->_dbTable->select()
      ->from('reset_password')
      ->where(array('user_id = "' . $user_id . '" AND ' .
        'DATE(expires) BETWEEN DATE_ADD(DATE(NOW()), INTERVAL -1 DAY)) AND DATE(NOW())'));
    $result = $this->_dbTable->fetchAll($select);
    return $result;
  }

  public function saveUserLoginInformation() {
    $userAgent = new Zend_Http_UserAgent();
    $device = $userAgent->getDevice();
    qaq($device);
    $browser = $device->getBrowser();
  }
}
