<?php

namespace Db\drivers\My;

use Db\drivers\My\MysqlInterface;
use Db\drivers\My\QueryCreator as QR;

class Mysql implements MysqlInterface {
	
	private static $dbconf = false;
	private static $active = false;
	private static $dbLink = null;
	
	private static $select;
	private static $where;
	
	private static $table;
	
	public function __construct()
	{
	//	if(is_null(self::$dbLink)) self::init();
	}
	
	public static function select($_select = false)
	{
		self::$select = $_select;
		
		return new static;
	}
	
	public static function where($_where = false)
	{
		self::$where = $_where;
		
		return new static;
	}
	
	public static function get($table)
	{
		return QR::go('select', array(
				'select' => self::$select,
				'where' => self::$where,
				'table' => $table
			)
		);
	}
	
	public static function connect($config)
	{
		self::$dbLink = mysql_connect($config['hostname'], $config['username'], $config['password']);
		mysql_select_db($config['database'], self::$dbLink);
		//var_dump(mysql_num_rows(mysql_query("select * FROM aa_langs")));
		
		//echo 'Connected successfully';
		//mysql_close($t);
		
	}
}