<?php

namespace Chipin\Widgets;

require_once 'my-php-libs/database.php';
use \MyPHPLibs\Database as DB;

function getWidgetById($id) {
  $rows = DB\select('*, DATE_FORMAT(ending,  "%m/%d/%Y") as ending', 'widgets',
                    'id = ?', array($id));
  if (count($rows) == 0) {
    throw new NoSuchWidget("Could not find widget with ID $id");
  }
  return current($rows);
}

class NoSuchWidget extends \Exception {}
