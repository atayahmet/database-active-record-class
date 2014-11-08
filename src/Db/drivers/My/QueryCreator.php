<?php namespace Db\drivers\My;

class QueryCreator {
	protected static $db;
	protected static $select = '';
	protected static $where = '';
	protected static $limit = '';
	protected static $offset;
	protected static $table;
	protected static $from;
	
	
	public static function init($config)
	{
		self::$db = $config;
	}
	
	private static function select($_select)
	{
		if(count($_select) > 0){
			foreach($_select as $s){
				self::$select .= empty(self::$select) ? $s : ','.$s;
			}
		}else{
			self::$select .= '*';
		}
		
		self::$select = 'SELECT ' . self::$select;
	}
	
	private static function from($table)
	{
		self::$from = 'FROM ' . $table;
	}
	
	private static function where($_where)
	{
		foreach($_where as $w){
			if(is_array($w)){
				foreach($w as $f => $__w){
					$fieldAndOp = self::checkOp($f);
					
					self::$where .= empty(self::$where) ? "{$fieldAndOp} '{$__w}'" : " AND {$fieldAndOp} '{$__w}'";
				}
			}else{
				self::$where .= empty(self::$where) ? $w : " AND {$w}";
			}
		}
		
		self::$where = 'WHERE ' . self::$where;
	}
	
	private static function limit($_limit = null)
	{
		if(!is_null($_limit) && !empty($_limit)){
			self::$limit = 'LIMIT ' . $_limit;
		}
	}
	
	private static function offset($_offset)
	{
		if(!is_null($_offset)){
			self::$offset = $_offset;
			
			if(!is_null(self::$limit) && !empty(self::$limit)){
				self::$limit = 'LIMIT ' . self::$offset . ',' . str_replace('LIMIT ','',self::$limit);
				
			}
		}
	}
	
	public static function get($query)
	{
		foreach($query as $method => $q){
			if(method_exists(new static, $method)){
				self::$method($q);
			}
		}
		
		return self::returnSql(__FUNCTION__);
		
		
	}
	
	private static function checkOp($op)
	{
		$oprs = array('!=','<>','<=','>=','<','>','=');
		
		foreach($oprs as $_op){
			preg_match('/' . preg_quote($_op) . '/',$op,$matches);
			
			if( count($matches) > 0){
				return $op;
			}
		}
		
		return $op.'=';
	}
	
	private static function returnSql($type)
	{
		switch($type){
			case "get";
				 $query =  self::$select . ' ' . self::$from . ' ' . self::$where . ' ' . self::$limit;
				break;
		}
		
		self::emptySqlVars();
		
		return $query;
		
	}
	
	protected static function emptySqlVars()
	{
		self::$select = '';
		self::$where = '';
		self::$from = '';
		self::$limit = '';
		self::$offset = '';
		self::$table = '';
	}
}
