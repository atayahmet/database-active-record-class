<?php namespace Db\drivers\My;

class QueryCreator {
	protected static $db;
	protected static $select = '';
	protected static $where = '';
	protected static $table;
	protected static $from;
	
	
	public static function init($config)
	{
		self::$db = $config;
	}
	
	public static function get($query)
	{
		foreach($query as $method => $q){
			self::$method($q);
		}
		
		return self::$select . ' ' . self::$from . ' ' . self::$where;
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
		//var_dump(self::$where);
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
}
