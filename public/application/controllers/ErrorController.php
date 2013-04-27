<?php

require_once 'my-php-libs/error-handling.php';
use \MyPHPLibs\ErrorHandling as EH;

class ErrorController extends Zend_Controller_Action {

  public function errorAction() {
    $errors = $this->_getParam('error_handler');

    if (!$errors || !$errors instanceof ArrayObject) {
      echo "Error occurred inside error-handler!! \$errors object was of unexpected type!";
      return;
    }

    switch ($errors->type) {
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        // 404 error -- controller or action not found
        $this->getResponse()->setHttpResponseCode(404);
        $priority = Zend_Log::NOTICE;
        $this->view->message = 'Page not found';
        break;
      default:
        // application error
        $this->getResponse()->setHttpResponseCode(500);
        $priority = Zend_Log::CRIT;
        $this->view->message = 'Application error';
        break;
    }

    // Log exception, if logger available
    $log = $this->getLog();
    if ($log) {
      $log->log($this->view->message, $priority, $errors->exception);
      $log->log('Request Parameters', $priority, $errors->request->getParams());
    }

    $report = EH\constructErrorReport($errors->exception);
    if ($this->getInvokeArg('displayExceptions') == true) {
      $this->view->report = $report;
    } else {
      EH\presentErrorReport($report, ADMIN_EMAIL);
      ob_flush();
      exit(-1);
      //$this->view->exceptionsSuppressed = true;
    }

    $this->view->request = $errors->request;
  }

  public function getLog() {
    $bootstrap = $this->getInvokeArg('bootstrap');
    $log = $bootstrap->getResource('Log');
    return $log ? $log : false;
  }
}

