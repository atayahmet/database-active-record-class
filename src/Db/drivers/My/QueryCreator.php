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
	protected static $or_like = '';
	protected static $not_like = '';
	protected static $or_not_like = '';
	protected static $limit = '';
	protected static $having = '';
	protected static $or_having = '';
	protected static $offset;
	protected static $table;
	protected static $from;
	protected static $groupby;
	protected static $orderby;
	protected static $distinct;
	
	
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
		
		self::$select = self::$select;
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
	
	private static function or_like($_or_like)
	{
		self::$or_like = self::likeVariation('LIKE', 'OR', $_or_like);
	}
	
	private static function not_like($_not_like)
	{
		self::$not_like = self::likeVariation('NOT LIKE', 'AND', $_not_like);
	}
	
	private static function or_not_like($_not_like)
	{
		self::$or_not_like = self::likeVariation('NOT LIKE', 'OR', $_not_like);
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
			self::$limit = ' LIMIT ' . $_limit;
		}
	}
	
	private static function offset($_offset)
	{
		if(!is_null($_offset)){
			self::$offset = $_offset;
			
			if(!is_null(self::$limit) && !empty(self::$limit)){
				self::$limit = ' LIMIT ' . self::$offset . ',' . str_replace('LIMIT ','',self::$limit);
			}
		}
	}

	private static function group_by($_groupby)
	{
		$groupTxt = '';
		
		foreach($_groupby as $_g){
			$groupTxt .= !empty($groupTxt) ? ',' . $_g : $_g;
		}
		
		if(!empty($groupTxt)) self::$groupby = ' GROUP BY ' . $groupTxt;
	}
	
	private static function order_by($_orderby)
	{
		$orderTxt = '';
		$rand = false;
		
		foreach($_orderby as $o){
			$field = key($o);
			$order = current($o);
			
			if($order != 'random' && (strtolower($order) == 'asc' || strtolower($order) == 'desc')){
				$o = $field . ' ' . strtoupper($order);
				$orderTxt .= !empty($orderTxt) ? ',' . $o : $o;
			}else{
				$rand = true;
			}
		}
		
		self::$orderby = ' ORDER BY ' . (!$rand ? $orderTxt : 'RAND()');
	}
	
	private static function distinct($_distinct)
	{
		self::$distinct = $_distinct;
	}
	
	private static function having($_having)
	{
		self::$having = self::havingVariation('AND', $_having);
	}
	
	private static function or_having($_or_having)
	{
		self::$or_having = self::havingVariation('OR', $_or_having);
	}
	
	private static function havingVariation($op, $_having)
	{
		$havingTxt = '';
		
		foreach($_having as $h){
			if(is_array($h)){
				foreach($h as $field => $_h){
					$fieldAndOp = self::checkOp($field) . $_h;
					$havingTxt .= " {$op} {$fieldAndOp}";
				}
			}else{
				$havingTxt .= " {$op} {$h}";
			}
		}
		
		return $havingTxt;
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
		
		return $op.' = ';
	}
	
	private static function returnSql($type)
	{
		switch($type){
			case "get";
				$query =  'SELECT ' . self::$distinct . self::$select . ' ' . self::$from . ' ' . 'WHERE ' . self::$where . self::$or_where 
						. ' AND ' . self::$where_in . ' OR ' . self::$or_where_in . ' AND ' 
						. self::$where_not_in . ' OR ' . self::$or_where_not_in . self::$like . self::$or_like . self::$not_like . self::$or_not_like 
						. ' ' . self::$groupby . ' HAVING ' . self::$having . self::$or_having . self::$orderby . self::$limit;
				$query = self::sqlRegulator($query);
				break;
		}
		
		self::emptySqlVars();
		
		return $query;
		
	}
	
	protected static function sqlRegulator($query)
	{
		foreach(array('AND','OR') as $op){
			if(preg_match('/WHERE(\s+)' . preg_quote($op) . '/', $query) > 0){
				$query = preg_replace('/WHERE(\s+)' . preg_quote($op) . '/', 'WHERE ', $query);
			}
			
			if(preg_match('/HAVING(\s+)' . preg_quote($op) . '/', $query) > 0){
				$query = preg_replace('/HAVING(\s+)' . preg_quote($op) . '/', 'HAVING ', $query);
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
		self::$or_like = '';
		self::$not_like = '';
		self::$or_not_like = '';
		self::$from = '';
		self::$limit = '';
		self::$offset = '';
		self::$table = '';
		self::$groupby = '';
		self::$orderby = '';
		self::$distinct = '';
		self::$having = '';
		self::$or_having = '';
	}
}
