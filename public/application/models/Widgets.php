<?php

class Application_Model_Widgets
{
	
	private $owner_id;
	private $_dbTable;
	
	public function __construct()
	{
		$this->_dbTable = new Application_Model_DbTable_Widgets();
	}
	
	public function setIdentity($owner_id)
	{
		$this->owner_id = $owner_id;
	}
	
	public function getUserWidgets()
	{
		$select = $this->_dbTable->select()
								->from('widgets', '*, DATE_FORMAT(ending,  "%m/%d/%Y") as ending')
								->where("owner_id = ".$this->owner_id);
		$widgets = $this->_dbTable->fetchAll($select);
		
		return $widgets;
	}
	
	public function addNewWidget($data)
	{
		$this->_dbTable->insert($data);
		return $this->_dbTable->getAdapter()->lastInsertId();
	}
	
	public function updateWidgetById($data, $id)
	{
		$this->_dbTable->update($data, "id=".$id);
	}
	
	public function getWidgetById($id)
	{
		$select = $this->_dbTable->select()
								->from('widgets', '*, DATE_FORMAT(ending,  "%m/%d/%Y") as ending')
								->where("owner_id = ".$this->owner_id." AND id=".$id);
		$widget = $this->_dbTable->fetchAll($select);
		return $widget[0];
	}
	
	public function deleteWidgetById($id)
	{
		$this->_dbTable->delete("id=".$id);
	}
	
	public function endWidget($id)
	{
		$this->_dbTable->update(array('ending' => date("Y-m-d", time() - (1 * 24 * 60 * 60))), 'id='.$id);
	}
	
	public function getAllWidgets()
	{
		$select = $this->_dbTable->select();
		$widgets = $this->_dbTable->fetchAll($select);
		
		return $widgets;
	}
	
	public function updateWidgetsProgress()
	{
		$select = $this->_dbTable->select();
		$result = $this->_dbTable->fetchAll($select);
		
		foreach ($result as $row) {
			$balance = $this->getBalance($row['currency'], $row['address']);
			$progress = $this->getProgress($balance, $row['goal']);
			$this->_dbTable->update(array('progress' => $progress, 'raised' => $balance), 'id='.$row['id']);
		}
	}
	
	public function getProgress($balance, $goal)
	{
		return $balance / $goal * 100;
	}
	
	public function getBalance($currency, $address)
	{
		$value = $this->getContentUsingCURL('http://blockchain.info/q/addressbalance/'.$address) / 100000000;
		$balance = substr($this->getContentUsingCURL('http://blockchain.info/tobtc?currency='.$currency.'&value='.$value), 0, 4);
		return $balance;
	}
	
	public function getContentUsingCURL($url)
	{
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

}

?>