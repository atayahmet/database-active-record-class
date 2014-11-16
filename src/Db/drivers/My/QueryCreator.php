<?php namespace Db\drivers\My;

class QueryCreator {
	protected static $db;
	protected static $select = '';
	protected static $select_max = '';
	protected static $select_min = '';
	protected static $select_avg = '';
	protected static $select_sum = '';
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
	protected static $join = '';
	protected static $insert = array();
	protected static $update = '';
	protected static $update_batch = '';
	protected static $offset;
	protected static $table = '';
	protected static $from = '';
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
	
	private static function selectVariation($field, $func)
	{
		if($field){
			self::select(array("\n{$func}(" . $field . ") as " . $field));
			
			preg_match('/' . preg_quote($func) . '\((.*?)\)(\s+)as(\s+)(.*?)/', self::$select,$matches);
			
			if(count($matches) > 1){
				self::$select = $matches[0] . $matches[1];
			}
		}
	}
	
	private static function select_max($field = false)
	{
		if($field){
			self::selectVariation($field, 'MAX');
		}
	}
	
	private static function select_min($field = false)
	{
		if($field){
			self::selectVariation($field, 'MIN');
		}
	}
	
	private static function select_avg($field = false)
	{
		if($field){
			self::selectVariation($field, 'AVG');
		}
	}
	
	private static function select_sum($field = false)
	{
		if($field){
			self::selectVariation($field, 'SUM');
		}
	}
	
	private static function from($table)
	{
		self::$from = 'FROM ' . $table;
	}
	
	private static function where($_where)
	{
		if(count($_where) > 0){
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
		if(count($_where_in) > 0){
			self::$where_in = ' AND ' . self::whereInVariation('IN','AND',$_where_in);
		}
	}
	
	private static function or_where_in($_or_where_in)
	{
		if(count($_or_where_in) > 0){
			self::$or_where_in = ' OR ' . self::whereInVariation('IN','OR',$_or_where_in);
		}
	}
	
	private static function where_not_in($_where_not_in)
	{
		if(count($_where_not_in) > 0){
			self::$where_not_in = ' AND ' . self::whereInVariation('NOT IN','AND',$_where_not_in);
		}
	}
	
	private static function or_where_not_in($_or_where_not_in)
	{
		if(count($_or_where_not_in) > 0){
			self::$or_where_not_in = ' OR ' . self::whereInVariation('NOT IN','OR',$_or_where_not_in);
		}
	}
	
	private static function like($_like)
	{
		if(count($_like) > 0){
			self::$like = self::likeVariation('LIKE', 'AND', $_like);
		}
	}
	
	private static function or_like($_or_like)
	{
		if(count($_or_like) > 0){
			self::$or_like = self::likeVariation('LIKE', 'OR', $_or_like);
		}
	}
	
	private static function not_like($_not_like)
	{
		if(count($_not_like) > 0){
			self::$not_like = self::likeVariation('NOT LIKE', 'AND', $_not_like);
		}
	}
	
	private static function or_not_like($_or_not_like)
	{
		if(count($_or_not_like) > 0){
			self::$or_not_like = self::likeVariation('NOT LIKE', 'OR', $_or_not_like);
		}
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
	
	private static function join($_join)
	{
		$joinTxt = '';
		
		if(count($_join) > 0){
			foreach($_join as $j){
				$joinTxt .= ' ' . self::joinType($j['type']) . ' ' . $j['table'] . ' ON(' . $j['compare'] . ') ';
			}
		}
		
		self::$join = $joinTxt;
	}
	
	private static function joinType($type = 'inner')
	{
		$joinTypes = array(
						'inner join',
						'cross join',
						'left join',
						'right join',
						'left outer join' => 'left outer',
						'right outer join' => 'right outer',
						'inner',
						'cross',
						'left',
						'right',
						'left outer join' => 'outer'
					);
		
		foreach($joinTypes as $k => $jt){
			if(preg_match('/' . preg_quote($jt) . '/',strtolower($type)) > 0){
				if(is_numeric($k)){
					if(preg_match('/(.*?)(\s+)(.*?)/', $jt) > 0){
						return strtoupper($jt);
					}else{
						return strtoupper($jt) . ' JOIN';
					}
				}else{
					return strtoupper($k);
				}
			}
		}
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
		$whereStr = '';
		
		if(is_array($where)){
			foreach ($where as $_where) {
				foreach($_where as $f => $w){
					$in = array();
					
					if(is_array($w)){
						foreach($w as $w_){
							$in[] = "'{$w_}'";
						}
					}else{
						$in[] = "'{$w}'";
					}
					
					$in = implode($in, ',');
					$whereStr .= empty($whereStr) ? "{$f} {$com} ({$in})" : " {$op} {$f} {$com} ({$in})";
				}
			}
		}
		
		return $whereStr;
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
		if(count($_orderby) > 0){
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
	}
	
	private static function distinct($_distinct)
	{
		self::$distinct = $_distinct;
	}
	
	private static function having($_having)
	{
		if(count($_having)){
			self::$having = ' HAVING ' . self::havingVariation('AND', $_having);
		}
	}
	
	private static function or_having($_or_having)
	{
		if(count($_or_having)){
			self::$or_having = self::havingVariation('OR', $_or_having);
		}
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
	
	public static function insert($parm)
	{
		self::$table = $parm['insert']['table'];
		$fields = implode(array_flip($parm['insert']['data']),',');
		$values = '(' . self::convertInsertData($parm['insert']['data']) . ')';
		
		self::$insert = array('fields' => $fields, 'value' => $values);
		
		return self::returnSql(__FUNCTION__);
	}
	
	public static function insert_batch($parm)
	{
		if(count($parm['insert_batch']['data']) > 0){
			self::$table = $parm['insert_batch']['table'];
			
			$values = array();
			
			foreach($parm['insert_batch']['data'] as $d){
				$values[] = '('. self::convertInsertData($d) . ')';
			}
			
			self::$insert = array('fields' => implode(array_flip($parm['insert_batch']['data'][0]),','), 'value' => implode($values,','));
			
			return self::returnSql('insert');
		}
	}
	
	public static function update($query)
	{
		foreach($query as $method => $q){
			if(method_exists(new static, $method) && $method != __FUNCTION__){
				self::$method($q);
			}
		}
		
		self::$table = $query['update']['table'];
		self::$update = self::convertUpdateData($query['update']['data']);
		
		return self::returnSql(__FUNCTION__);
	}

	public static function delete($parm)
	{
		foreach($parm as $method => $q){
			if(method_exists(new static, $method)){
				self::$method($q);
			}
		}

		self::$table = $parm['table'];
		return self::returnSql(__FUNCTION__);
	}

	public static function update_batch($parm)
	{
		$rawParm = $parm['update_batch'];
		
		self::$table = $rawParm['table'];
		$refCol = $rawParm['ref'];
		$setCol = $rawParm['data'][0];
		unset($setCol[$refCol]);
		$setCol = array_flip($setCol);
		
		$case = array();
		$when = array();
		$whereIn = array();
		
		foreach($setCol as $col){
			$case[$col] = $col . ' = CASE ';
			
			foreach($rawParm['data'] as $d){
				$whereIn[$d[$refCol]] = $d[$refCol];
				$when[$col][] = "WHEN {$refCol} = '{$d[$refCol]}' THEN '{$d[$col]}'";
			}
			
			$case[$col] .= implode($when[$col], ' ');
			$case[$col] .= " ELSE {$col} END";
		}
		
		self::$where_in = $refCol . ' IN(' . self::convertInsertData($whereIn) . ')';
		self::$update = implode($case,',');
		
		return self::returnSql('update');
	}
	
	private static function convertUpdateData($data = false)
	{
		if(is_array($data)){
			$_set = array();
			
			foreach($data as $k => $d){
				$_set[] = $k . '=' . "'{$d}'"; 
			}
			
			return implode($_set, ',');
		}
	}
	
	private static function convertInsertData($data = false)
	{
		if(is_array($data)){
			return "'" . preg_replace_callback("/(.*?),/",function($matches){
				return preg_replace('/,(.*?)/', "$1',", $matches[0]."'");
			},implode($data, ',')) . "'";
		}
		
		return '';
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
				$query =  "SELECT\n" . self::$distinct . self::$select . ' ' . self::$from . self::$join . "\n" . 'WHERE ' . self::$where . self::$or_where 
						. self::$where_in . self::$or_where_in. self::$where_not_in
						. self::$or_where_not_in . self::$like . self::$or_like . self::$not_like . self::$or_not_like 
						. "\n" . self::$groupby . self::$having . self::$or_having . self::$orderby . self::$limit;

				break;
				
			case "insert";
				if(is_array(self::$insert)){
					$query = "INSERT INTO " . self::$table . "(" . self::$insert['fields'] . ") VALUES" . self::$insert['value'];
				}
				break;
				
			case "update";
				$query = "UPDATE " . self::$table . " SET " . self::$update . " WHERE " . self::$where . self::$or_where 
						. self::$where_in . self::$or_where_in. self::$where_not_in. self::$or_where_not_in . self::$like 
						. self::$or_like . self::$not_like . self::$or_not_like . self::$limit;
				break;

			case "delete";
				$query = "DELETE FROM " . self::$table . " WHERE " . self::$where . self::$or_where
					. self::$where_in . self::$or_where_in. self::$where_not_in. self::$or_where_not_in . self::$like
					. self::$or_like . self::$not_like . self::$or_not_like . self::$limit;
				break;
		}
		//exit($query);
		$query = self::sqlRegulator($query);
		var_dump($query);
		self::emptySqlVars();
		
		return $query;
		
	}
	
	protected static function sqlRegulator($query)
	{
		foreach(array('AND','OR') as $op){
			if(preg_match('/WHERE(\s+)' . preg_quote($op) . '/', $query) > 0){
				$query = preg_replace('/WHERE(\s+)' . preg_quote($op) . '/', 'WHERE ', $query);
			}
			
			if(preg_match('/WHERE(\s+)[a-zA-Z]/', $query) < 1){
				$query = preg_replace('/WHERE/', '', $query);
			}
			
			if(preg_match('/HAVING(\s+)' . preg_quote($op) . '/', $query) > 0){
				$query = preg_replace('/HAVING(\s+)' . preg_quote($op) . '/', 'HAVING ', $query);
			}
		}
		
		return preg_replace('/(\s+)/', ' ',$query);
	}
	
	protected static function emptySqlVars()
	{
		self::$select = '';
		self::$select_max = '';
		self::$select_min = '';
		self::$select_avg = '';
		self::$select_sum = '';
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
		self::$insert = array();
		self::$update = '';
		self::$update_batch = '';
		self::$join = '';
	}
}
