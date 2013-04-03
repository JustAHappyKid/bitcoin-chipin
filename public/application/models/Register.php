<?php

class Application_Model_Register
{
	private $_dbTable;
	
	public function __construct()
	{
		$this->_dbTable = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function createUser($data)
	{
		$this->_dbTable->insert('users', $data);
	}
	
	public function updateUsersPassword($data, $id)
	{
		$this->_dbTable->update('users', $data, $id);
	}
	
	public function ifExpired($code, $user_id){
		$select = $this->_dbTable
			->select()
			->from('reset_password')
			->where('user_id = "'.$user_id.'" AND code="'.$code.'" AND expires >= NOW() AND status = 1')
			->order('id DESC')
			->limit('1');
		
		$result = $this->_dbTable->fetchAll($select);
		if(!isset($result[0])){
			return FALSE;
		}
		else{
			return $result[0];
		}
	}
	
	public function removeConfirmationLink($code, $user_id)
	{
		$this->_dbTable->delete('reset_password', array(
			'user_id = ?' => $user_id,
			'code = ?' => $code
		));
	}
	
}

?>