<?php

namespace Db\drivers\My;

use Db\drivers\My\MysqlInterface;
use Db\drivers\My\QueryCreator as QR;

class Mysql implements MysqlInterface {
	
	private static $dbconf = false;
	private static $active = false;
	private static $dbLink = null;
	
	private static $select = array();
	private static $where = array();
	private static $or_where = array();
	private static $where_in = array();
	private static $or_where_in = array();
	private static $limit = null;
	private static $offset = null;
	private static $table;
	
	protected static $query;
	protected static $qResult;
	protected static $dbErr;
	protected static $Queries = array();
	protected static $dbClosed = false;
	
	public function __construct()
	{
		//	if(is_null(self::$dbLink)) self::init();
	}
	
	public static function select($_select = null)
	{
		self::$select[] = $_select;
		
		return new static;
	}
	
	public static function where($_where = null)
	{
		$args = func_get_args();
		
		if(count($args) == 2){
			self::$where[] = array($args[0] => $args[1]);
		}else{
			self::$where[] = $_where;
		}
		
		return new static;
	}
	
	public static function or_where($_or_where = null)
	{
		$args = func_get_args();
		
		if(count($args) == 2){
			self::$or_where[] = array($args[0] => $args[1]);
		}else{
			self::$or_where[] = $_or_where;
		}
		
		return new static;
	}
	
	public static function where_in()
	{
		$args = func_get_args();
		
		if(count($args) == 2){
			self::$where_in[] = array($args[0] => $args[1]);
		}
		
		return new static;
	}
	
	public static function or_where_in()
	{
		$args = func_get_args();
		
		if(count($args) == 2){
			self::$or_where_in[] = array($args[0] => $args[1]);
		}
		
		return new static;
	}
	
	public static function from($table = null)
	{
		self::$table = $table;
		
		return new static;
	}
	
	public static function limit($_limit = null, $_offset = null)
	{
		self::$limit = $_limit;
		
		if(!is_null($_offset)){
			self::$offset = $_offset;
		}
		
		return new static;
	}
	
	public static function offset($_offset = null)
	{
		self::$offset = $_offset;
		
		return new static;
	}
	
	public static function get($table = false)
	{
		if(is_null(self::$table)){
			self::$table = $table;
		}
		
		self::$query = QR::get(
			array(
				'select' => self::$select,
				'where' => self::$where,
				'or_where' => self::$or_where,
				'where_in' => self::$where_in,
				'or_where_in' => self::$or_where_in,
				'limit' => self::$limit,
				'offset' => self::$offset,
				'from' => self::dbprefix(self::$table)
			)
		);
		
		self::execute();
		var_dump(self::$query);
		return clone new static;
	}
	
	protected static function execute()
	{
		self::$dbLink = mysql_connect(self::$dbconf['hostname'], self::$dbconf['username'], self::$dbconf['password']);
		
		if(!self::$dbLink){
			self::$dbErr = 'Check database connections info';
		}
		
		mysql_select_db(self::$dbconf['database'], self::$dbLink);
		mysql_set_charset('utf8',self::$dbLink); 
		
		
		self::$Queries[md5(self::$query)]['query'] = self::$query;
		
		$start = microtime();
		self::$qResult = mysql_query(self::$query);
		$stop = microtime();
		
		self::$Queries[md5(self::$query)]['duration'] = number_format($stop - $start,4,',','.');
		
		if(!self::$qResult){
			self::$dbErr = mysql_error();
		}
		
		self::emptySqlVars();
	}
	
	public static function dbprefix($table = null)
	{
		if(!is_null($table) && preg_match('/' . preg_quote(self::$dbconf['dbprefix']) . '(.*?)/', $table) < 1){
			return self::$dbconf['dbprefix'] . $table;
		}
		
		return $table;
	}
	
	public static function num_rows()
	{
		return mysql_num_rows(self::$qResult);
	}
	
	public static function row($num = 0)
	{
		$result = self::result();
		
		if(isset($result->{$num})){
			return $result->{$num};
		}
	}
	
	public static function row_array($num = 0)
	{
		$result = self::result_array();
		
		if(isset($result[$num])){
			return $result[$num];
		}
	}
	
	public static function result()
	{
		$i = 0;
		$j = 0;
		
		$ResultObj = new \stdClass();
		$fields = mysql_num_fields(self::$qResult);
		
		while($obj = mysql_fetch_object(self::$qResult)){
			$j = 0;
			
			$ResultObj->$i = new \stdClass();
			
			while($j < $fields){
				$field = mysql_field_name(self::$qResult, $j);
				$ResultObj->{$i}->$field = $obj->$field;
				$j++;
			}
			
			$i++;
		}
		
		self::dbConnectionClose();
		
		return $ResultObj;
	}
	
	public static function result_array()
	{
		if(self::$qResult){
			$i = 0;
			$j = 0;
			
			$ResultArr = array();
			$fields = mysql_num_fields(self::$qResult);
			
			while($arr = mysql_fetch_array(self::$qResult)){
				$j = 0;
				
				while($j < $fields){
					$field = mysql_field_name(self::$qResult, $j);
					$ResultArr[$i][$field] = $arr[$field];
					
					$j++;
				}
				
				$i++;
			}
			
			self::dbConnectionClose();
			
			return $ResultArr;
		}
	}
	
	public static function init(&$config)
	{
		self::$dbconf = $config;
	}
	
	protected static function emptySqlVars()
	{
		self::$select = array();
		self::$where = array();
		self::$or_where = array();
		self::$where_in = array();
		self::$or_where_in = array();
		self::$limit = '';
		self::$offset = '';
		self::$table = '';
	}

	protected static function dbConnectionClose()
	{
		if(!self::$dbClosed){
			self::$dbClosed = true;
			mysql_close(self::$dbLink);
		}
	}
}