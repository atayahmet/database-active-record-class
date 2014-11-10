<?php namespace Db\drivers\My;

class QueryCreator {
	protected static $db;
	protected static $select = '';
	protected static $where = '';
	protected static $or_where = '';
	protected static $where_in = '';
	protected static $where_not_in = '';
	protected static $or_where_not_in = '';
	protected static $or_where_in = '';
	protected static $like = '';
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
				self::$or_where .= empty(self::$or_where) && empty(self::$where) ? "({$w})" : " OR ({$w})";
			}
		}
	}
	
	private static function where_in($_where_in)
	{
		self::$where_in = self::whereInVariation('IN','AND',$_where_in);
	}
	
	private static function or_where_in($_where_in)
	{
		self::$or_where_in = self::whereInVariation('IN','OR',$_where_in);
	}
	
	private static function where_not_in($_where_not_in)
	{
		self::$where_not_in = self::whereInVariation('NOT IN','AND',$_where_not_in);
	}
	
	private static function or_where_not_in($_or_where_not_in)
	{
		self::$or_where_not_in = self::whereInVariation('NOT IN','OR',$_or_where_not_in);
	}
	
	private static function like($_like)
	{
		self::$like = self::likeVariation('LIKE', 'AND', $_like);
	}
	
	private static function likeVariation($compare, $operator, $_like)
	{
		$likeTxt = '';
		
		if(is_array($_like)){
			foreach($_like as $l){
				$field = key($l);
				$likeTxt .= ' ' . $operator . ' ' . $field . ' ' . $compare . ' ' . self::likePos(current($l));
			}
		}
		
		return $likeTxt;
	}
	
	private static function likePos($like)
	{
		if($like['pos'] == 'both'){
			return " '%{$like['val']}%'";
		}
		
		elseif($like['pos'] == 'before'){
			return " '%{$like['val']}'";
		}
		
		elseif($like['pos'] == 'after'){
			return " '{$like['val']}%'";
		}
		
		elseif($like['pos'] == 'none'){
			return " '{$like['val']}'";
		}
	}
	
	private static function whereInVariation($com, $op, $where)
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
				$query =  self::$select . ' ' . self::$from . ' ' . 'WHERE ' . self::$where . self::$or_where . ' AND ' . self::$where_in . ' OR ' . self::$or_where_in . ' AND ' . self::$where_not_in . ' OR ' . self::$or_where_not_in . self::$like . ' ' . self::$limit;
				$query = self::whereRegulator($query);
				break;
		}
		
		self::emptySqlVars();
		
		return $query;
		
	}
	
	protected static function whereRegulator($query)
	{
		foreach(array('AND','OR') as $op){
			if(preg_match('/WHERE(\s+)' . preg_quote($op) . '/', $query) > 0){
				return preg_replace('/WHERE(\s+)' . preg_quote($op) . '/', 'WHERE ', $query);
			}
		}
		
		return $query;
	}
	
	protected static function emptySqlVars()
	{
		self::$select = '';
		self::$where = '';
		self::$or_where = '';
		self::$where_in = '';
		self::$where_not_in = '';
		self::$or_where_not_in = '';
		self::$or_where_in = '';
		self::$like = '';
		self::$from = '';
		self::$limit = '';
		self::$offset = '';
		self::$table = '';
	}
}
