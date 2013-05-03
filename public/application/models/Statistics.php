<?php

require_once 'chipin/widgets.php';
use \Chipin\Widgets;

class Application_Model_Statistics
{
	
	private $_dbTable;
	
	public function __construct()
	{
		$this->_dbTable = Zend_Db_Table::getDefaultAdapter();
	}

	public function getTopCountries()
	{
		$select = $this->_dbTable->select()
			->from('widgets', 'count(location) AS count, location AS name')
			->group('location')
			->order('count DESC')
			->limit(5);
		
		$result = $this->_dbTable->fetchAll($select);
		return $result;
	}
	
	public function getTopActiveUsers()
	{
		$select = $this->_dbTable->select()
			->from('widgets', array('count(widgets.id) AS count'))
			->join('users', 'users.id = widgets.owner_id', array('users.username AS username'))
			->group('username')
			->order('count DESC')
			->limit(5);

		$result = $this->_dbTable->fetchAll($select);
		return $result;
	}
	
	public function getFastestGrowingWidgets()
	{
		$select = $this->_dbTable->select()
			->from('widgets', array('title', 'progress'))
			->order('progress DESC')
			->limit(5);

		$result = $this->_dbTable->fetchAll($select);
		return $result;
	}
	
	public function getTotalNumberOfWidgetsCreated()
	{
		$select = $this->_dbTable->select()
			->from('widgets', array('count(*) AS count'));
		$total_widgets_created = $this->_dbTable->fetchAll($select);
		$result['total']['count'] = $total_widgets_created[0]['count'];
		
		
		$select = $this->_dbTable->select()
			->from('widgets', array('count(*) AS count'))
			->where('DATE(created) < DATE_ADD(DATE(NOW()), INTERVAL -1 MONTH)');
		$before_last_month = $this->_dbTable->fetchAll($select);
		$result['total']['progress'] = @($result['total']['count'] - $before_last_month[0]['count']) * 100 / $result['total']['count'];
		
		return $result;
	}

	public function getNewWidgetsStatistics()
	{
		$stats['today'] = $this->getNewWidgetsStatisticsForToday();
		$stats['month'] = $this->getNewWidgetsStatisticsForThisMonth();
		$stats['active'] = $this->getNewActiveWidgetsStatistics();
		return $stats;
	}

	public function getNewWidgetsStatisticsForToday()
	{
		$select = $this->_dbTable->select()
			->from('widgets', array('count(*) AS count'))
			->where('DATE(created) = DATE(NOW())');
		
		$today = $this->_dbTable->fetchAll($select);
		$today = $today[0]['count'];
		$result['count'] = $today;
		
		$select = $this->_dbTable->select()
			->from('widgets', array('count(*) AS count'))
			->where('DATE(created) BETWEEN DATE_ADD(DATE(NOW()), INTERVAL -2 DAY) AND DATE_ADD(DATE(NOW()), INTERVAL -1 DAY)');
		
		$yesterday = $this->_dbTable->fetchAll($select);
		$yesrterday = $yesterday[0]['count'];
		$result['progress'] = @(($today - $yesrterday)/$today * 100);
		return $result;
	}

	public function getNewWidgetsStatisticsForThisMonth()
	{
		$select = $this->_dbTable->select()
			->from('widgets', array('count(*) AS count'))
			->where('DATE(created) BETWEEN DATE_ADD(DATE(NOW()), INTERVAL -1 MONTH) AND DATE(NOW())');
		
		$this_month = $this->_dbTable->fetchAll($select);
		$this_month = $this_month[0]['count'];
		$result['count'] = $this_month;
		
		$select = $this->_dbTable->select()
			->from('widgets', array('count(*) AS count'))
			->where('DATE(created) BETWEEN DATE_ADD(DATE(NOW()), INTERVAL -2 MONTH) AND DATE_ADD(DATE(NOW()), INTERVAL -1 MONTH)');
		
		$last_month = $this->_dbTable->fetchAll($select);
		$last_month = $last_month[0]['count'];
		$result['progress'] = @(($this_month - $last_month)/$this_month * 100);
		return $result;
	}

	public function getNewActiveWidgetsStatistics()
	{
		$select = $this->_dbTable->select()
			->from('widgets', array('count(*) AS count'))
			->where('DATE(ending) > DATE(NOW()) AND DATE(created) BETWEEN DATE_ADD(DATE(NOW()), INTERVAL -1 MONTH) AND DATE(NOW())');
		
		$this_month = $this->_dbTable->fetchAll($select);
		$this_month = $this_month[0]['count'];
		$result['count'] = $this_month;
		
		$select = $this->_dbTable->select()
			->from('widgets', array('count(*) AS count'))
			->where('DATE(ending) < DATE(NOW()) AND DATE(created) BETWEEN DATE_ADD(DATE(NOW()), INTERVAL -2 MONTH) AND DATE_ADD(DATE(NOW()), INTERVAL -1 MONTH)');
		
		$last_month = $this->_dbTable->fetchAll($select);
		$last_month = $last_month[0]['count'];
		$result['progress'] = @(($this_month - $last_month)/$this_month * 100);
		return $result;
	}
	
	public function getEndingWidgetsStatistics()
	{
		$stats['today'] = $this->getEndingWidgetsStatisticsForToday();
		$stats['month'] = $this->getEndingWidgetsStatisticsForThisMonth();
		return $stats;		
	}

	public function getEndingWidgetsStatisticsForToday()
	{
		$select = $this->_dbTable->select()
			->from('widgets', array('count(*) AS count'))
			->where('DATE(ending) = DATE(NOW())');
		
		$today = $this->_dbTable->fetchAll($select);
		$today = $today[0]['count'];
		$result['count'] = $today;
		
		$select = $this->_dbTable->select()
			->from('widgets', array('count(*) AS count'))
			->where('DATE(ending) BETWEEN DATE_ADD(DATE(NOW()), INTERVAL -2 DAY) AND DATE_ADD(DATE(NOW()), INTERVAL -1 DAY)');
		
		$yesterday = $this->_dbTable->fetchAll($select);
		$yesrterday = $yesterday[0]['count'];
		$result['progress'] = @(($today - $yesrterday)/$today * 100);
		return $result;
	}

	public function getEndingWidgetsStatisticsForThisMonth()
	{
		$select = $this->_dbTable->select()
			->from('widgets', array('count(*) AS count'))
			->where('DATE(ending) BETWEEN DATE_ADD(DATE(NOW()), INTERVAL -1 MONTH) AND DATE(NOW())');
		
		$this_month = $this->_dbTable->fetchAll($select);
		$this_month = $this_month[0]['count'];
		$result['count'] = $this_month;
		
		$select = $this->_dbTable->select()
			->from('widgets', array('count(*) AS count'))
			->where('DATE(ending) BETWEEN DATE_ADD(DATE(NOW()), INTERVAL -2 MONTH) AND DATE_ADD(DATE(NOW()), INTERVAL -1 MONTH)');
		
		$last_month = $this->_dbTable->fetchAll($select);
		$last_month = $last_month[0]['count'];
		$result['progress'] = @(($this_month - $last_month)/$this_month * 100);
		return $result;
	}

	public function getAllWidgets() {
		return Widgets\getAll();
	}

	public function getRecentWidgets()
	{
		$select = $this->_dbTable->select()
			->from('widgets', '*')
			->where('DATE(created) > DATE_ADD(DATE(NOW()), INTERVAL -1 MONTH)');
		$widgets = $this->_dbTable->fetchAll($select);
		
		return $widgets;
	}

}

