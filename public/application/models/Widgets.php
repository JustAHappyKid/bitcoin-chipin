<?php

require_once 'chipin/bitcoin.php';
use \Chipin\Bitcoin;

class Application_Model_Widgets {

  private $ownerID;
  private $_dbTable;

  public function __construct() {
    $this->_dbTable = new Application_Model_DbTable_Widgets();
  }

  public function setIdentity($ownerID) {
    $this->ownerID = $ownerID;
  }

  public function getUserWidgets() {
    $select = $this->_dbTable->select()
                ->from('widgets', '*, DATE_FORMAT(ending, "%m/%d/%Y") as ending')
                ->where("owner_id = " . $this->ownerID);
                //->where("ownerID = ".$this->ownerID);
    $widgets = $this->_dbTable->fetchAll($select);
    return $widgets;
  }

  /*
  public function addNewWidget($data) {
    debug('Inserting widget: ' . asString($data));
    $this->_dbTable->insert($data);
    return $this->_dbTable->getAdapter()->lastInsertId();
  }
  */

  public function updateWidgetById($data, $id) {
    $this->_dbTable->update($data, "id=".$id);
  }

  public function getWidgetById($id) {
    $select = $this->_dbTable->select()
                ->from('widgets', '*, DATE_FORMAT(ending,  "%m/%d/%Y") as ending')
                ->where("owner_id = " . $this->ownerID . " AND id = " . $id);
                //->where("ownerID = ".$this->ownerID." AND id=".$id);
    $widget = $this->_dbTable->fetchAll($select);
    return $widget[0];
  }

  public function deleteWidgetById($id) {
    $this->_dbTable->delete("id=".$id);
  }

  public function endWidget($id) {
    $this->_dbTable->update(array('ending' => date("Y-m-d", time() - (1 * 24 * 60 * 60))), 'id='.$id);
  }

  public function getAllWidgets() {
    $select = $this->_dbTable->select();
    $widgets = $this->_dbTable->fetchAll($select);
    return $widgets;
  }

  public function updateWidgetsProgress() {
    $select = $this->_dbTable->select();
    $result = $this->_dbTable->fetchAll($select);
    foreach ($result as $row) {
      $balance = Bitcoin\getBalance($row['address'], $row['currency']);
      $progress = $this->getProgress($balance, $row['goal']);
      $this->_dbTable->update(array('progress' => $progress, 'raised' => $balance), 'id='.$row['id']);
    }
  }

  public function getProgress($balance, $goal) {
    return $balance / $goal * 100;
  }
}
