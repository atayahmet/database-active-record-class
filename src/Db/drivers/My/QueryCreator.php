<?php namespace Db\drivers\My;

class QueryCreator {
	protected static $db;
	protected static $select = '';
	protected static $where = '';
	protected static $or_where = '';
	protected static $where_in = '';
	protected static $or_where_in = '';
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
	}
	
	private static function or_where($_or_where)
	{
		foreach($_or_where as $w){
			$whereTxt = '';
			
			if(is_array($w)){
				foreach($w as $f => $__w){
					$fieldAndOp = self::checkOp($f);
					$whereTxt .= empty($whereTxt) ? "{$fieldAndOp} '{$__w}'" : " AND {$fieldAndOp} '{$__w}'";
				}
				
				self::$or_where .= ' OR (' . $whereTxt . ')';
			}else{
				self::$or_where .= empty(self::$or_where) ? $w : " OR {$w}";
			}
		}
	}
	
	private static function where_in($_where_in)
	{
		self::$where_in = self::whereVariation('IN','AND',$_where_in);
	}
	
	private static function or_where_in($_where_in)
	{
		self::$or_where_in = self::whereVariation('IN','AND',$_where_in);
	}
	
	private static function whereVariation($com, $op, $where)
	{
		$_where = '';
		
		if(is_array($where)){
			foreach($where as $w){
				foreach($w as $f => $__w){
					$_in = '';
					
					if(is_array($__w)){
						foreach($__w as $w_){
							$_in .= (empty($_in) ? "'$w_'" : ",'$w_'");
						}
					}else{
						$_in = (empty($_in) ? "'$__w'" : ",'$__w'");
					}
					
					$_where .= empty($_where) ? "{$f} {$com} ({$_in})" : " {$op} {$f} {$com} ({$_in})";
				}
			}
		}
		
		return $_where;
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
				$orWhere = self::whereRegulator('or_where', self::$or_where);
				$whereIn = self::whereRegulator('where_in', self::$where_in);
				$orWhereIn = self::whereRegulator('or_where_in', self::$or_where_in);
				
				$query =  self::$select . ' ' . self::$from . ' ' . 'WHERE ' . self::$where . $orWhere . $whereIn . $orWhereIn . ' ' . self::$limit;
				break;
		}
		
		self::emptySqlVars();
		
		return $query;
		
	}
	
	protected static function whereRegulator($type = false, $where = false)
	{
		if($type && $where){
			if($type == 'or_where' && (empty(self::$where) && preg_match('/OR (.*?)/',$where) > 0)){
				return $or_where = substr($where,4);
			}
			
			elseif($type == 'where_in' && (!empty(self::$where) || !empty(self::$or_where))){
				return ' AND ' . $where;
			}

			elseif($type == 'or_where_in' && (!empty(self::$where) || !empty(self::$or_where) || !empty(self::$where_in))){
				return ' OR ' . $where;
			}
		}
		
		return $where;
	}
	
	protected static function emptySqlVars()
	{
		self::$select = '';
		self::$where = '';
		self::$or_where = '';
		self::$where_in = '';
		self::$or_where_in = '';
		self::$from = '';
		self::$limit = '';
		self::$offset = '';
		self::$table = '';
	}
}
