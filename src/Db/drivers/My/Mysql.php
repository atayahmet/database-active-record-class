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
	private static $table;
	protected static $query;
	protected static $qResult;
	protected static $dbErr;
	
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
		self::$where[] = $_where;
		
		return new static;
	}
	
	public static function from($table = null)
	{
		self::$table = $table;
		
		return new static;
	}
	
	public static function get($table = false)
	{
		
		!$table || $table = self::dbprefix($table);
		
		self::$query = QR::get(
			array(
				'select' => self::$select,
				'where' => self::$where,
				'from' => $table
			)
		);
		
		self::execute();
		
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
		
		self::$qResult = mysql_query(self::$query);
		
		if(!self::$qResult){
			self::$dbErr = mysql_error();
		}
		
		mysql_close(self::$dbLink);
	}
	
	public static function dbprefix($table = null)
	{
		return self::$dbconf['dbprefix'] . $table;
	}
	
	public static function num_rows()
	{
		return mysql_num_rows(self::$qResult);
	}
	
	public static function row()
	{
		
	}
	
	public static function result()
	{
		
	}
	
	public static function result_array()
	{
		
	}
	
	public static function init(&$config)
	{
		self::$dbconf = $config;
	}
}