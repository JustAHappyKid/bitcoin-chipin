<?php

require_once 'my-php-libs/url.php';                     # constructUrlFromRelativeLocation
require_once 'my-php-libs/webapp/current-request.php';  # CurrentRequest\...

use \MyPHPLibs\URL, \MyPHPLibs\Webapp\CurrentRequest;

class Application_Form_Signup extends Zend_Form {

  private $captchasDir = 'images/captcha';

  public function init() {
    $localPathToCaptchas = DOC_ROOT . '/' . $this->captchasDir;
    if (!is_dir($localPathToCaptchas)) {
      throw new Exception("Directory for storing captcha images does not exist");
    } else if (!is_writable($localPathToCaptchas)) {
      throw new Exception("Directory for storing captchas is not writable by application");
    }
    $thisURL = CurrentRequest\getURL();
    $captchasDirURL = URL\constructUrlFromRelativeLocation($thisURL, '/' . $this->captchasDir);
    $captcha = new Zend_Form_Element_Captcha('captcha', array(
      'captcha' => array(
        'captcha' => 'Image',
        'wordLen' => 4,
        'timeout' => 300,
        'width' => '100',
        'height' => '41',
        'font' => 'fonts/arial.ttf',
        'imgUrl' => $captchasDirURL,
        'imgDir' => $this->captchasDir)
    ));
    $captcha->setDecorators(array(
      array('Description', array('escape' => false, 'tag' => 'div')),
      array('HtmlTag', array('tag' => 'div', 'class' => 'field')),
    ))->setAttrib('placeholder', 'Captcha');
    $this->addElement($captcha);
  }
}
