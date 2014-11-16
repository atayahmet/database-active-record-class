<?php

namespace Db\drivers\My;

/**
 * Database Active Record
 *
 *
 * Why was heard to have to write such a library might ask.
 * A CMS project I had to do database operations outside the MVC framework I worked on
 *
 *
 * I think will be useful to a alot of people of this library
 * The used of  codeigniter active record class interface
 *
 *
 * See https://github.com/atayahmet/database-active-record-class
 * for the full documentary.
 */

use Db\drivers\My\MysqlInterface;
use Db\drivers\My\QueryCreator as QR;
use Db\DbException as ErrorCatcher;

class Mysql implements MysqlInterface {
	// Database configuration
	private static $dbconf = false;
	private static $active = false;

	// database connection object
	private static $dbLink = null;

	// SELECT string
	private static $select = array();

	// SELECT MAX()
	private static $select_max = '';

	// SELECT MIN()
	private static $select_min = '';

	// SELECT AVG()
	private static $select_avg = '';

	// SELECT SUM
	private static $select_sum = '';

	// WHERE combination
	private static $where = array();

	// WHERE OR combination
	private static $or_where = array();

	// WHERE IN combination
	private static $where_in = array();

	// WHERE NOT IN combination
	private static $where_not_in = array();

	// WHERE OR NOT IN combination
	private static $or_where_not_in = array();

	// WHERE OR IN combination
	private static $or_where_in = array();

	// LIKE combination
	private static $like = array();

	// OR LIKE combination
	private static $or_like = array();

	// NOT LIKE combination
	private static $not_like = array();

	// OR NOT LIKE combination
	private static $or_not_like = array();

	// GROUP BY combination
	private static $group_by = array();

	// ORDER BY combination
	private static $order_by = array();

	// HAVING combination
	private static $having = array();

	// OR HAVING combination
	private static $or_having = array();

	// (INNER, LEFT, RIGHT, OUTER, CROSS) JOIN combination
	private static $join = array();

	// LIMIT
	private static $limit = null;

	// OFFSET
	private static $offset = null;

	// SELECT DISTINCT()
	private static $distinct = '';

	// Query table
	private static $table;

	// INSERT
	private static $insert = array();

	// Multi INSERT
	private static $insert_batch = array();

	// Last insert id
	private static $insert_id = null;

	// Data collectors for update query
	private static $set = array();

	// UPDATE
	private static $update = array();

	// Multi UPDATE
	private static $update_batch = array();

	// Query string
	protected static $query;

	// query result
	protected static $qResult;

	// database error control variable
	protected static $dbErr;

	// query repository
	protected static $Queries = array();

	// Database connection close check variable
	protected static $dbClosed = false;

	/**
	 * Query select collectors
	 *
	 * @param string $_select
	 * @return new static
	 */
	public static function select($_select = null)
	{
		self::$select[] = $_select;
		
		return new static;
	}

	/**
	 * Query select MAX collectors
	 *
	 * @param string $field
	 * @return new static
	 */
	public static function select_max($field = false)
	{
		if($field){
			self::$select_max = $field;
		}
		
		return new static;
	}

	/**
	 * Query select MIN collectors
	 *
	 * @param string $field
	 * @return new static
	 */
	public static function select_min($field = false)
	{
		if($field){
			self::$select_min = $field;
		}
		
		return new static;
	}

	/**
	 * Query select AVG collectors
	 *
	 * @param string $field
	 * @return new static
	 */
	public static function select_avg($field = false)
	{
		if($field){
			self::$select_avg = $field;
		}
		
		return new static;
	}

	/**
	 * Query select SUM collectors
	 *
	 * @param string $field
	 * @return new static
	 */
	public static function select_sum($field = false)
	{
		if($field){
			self::$select_sum = $field;
		}
		
		return new static;
	}

	/**
	 * Query WHERE parameter collectors
	 *
	 * @param string/array $_where
	 * @param string/integer $value (optional)
	 * @return new static
	 */
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

	/**
	 * Query WHERE OR parameter collectors
	 *
	 * @param string/array $_or_where
	 * @param string/integer $value (optional)
	 * @return new static
	 */
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

	/**
	 * Query WHERE IN parameter collectors
	 *
	 * @param string $_or_where
	 * @param string/integer/array
	 * @return new static
	 */
	public static function where_in()
	{
		$args = func_get_args();
		
		if(count($args) == 2){
			self::$where_in[] = array($args[0] => $args[1]);
		}
		
		return new static;
	}

	/**
	 * Query WHERE OR IN parameter collectors
	 *
	 * @param string $_or_where_in
	 * @param string/integer/array
	 * @return new static
	 */
	public static function or_where_in()
	{
		$args = func_get_args();
		
		if(count($args) == 2){
			self::$or_where_in[] = array($args[0] => $args[1]);
		}
		
		return new static;
	}

	/**
	 * Query WHERE NOT IN parameter collectors
	 *
	 * @param string $_where_not_in
	 * @param string/integer/array
	 * @return new static
	 */
	public static function where_not_in()
	{
		$args = func_get_args();
		
		if(count($args) == 2){
			self::$where_not_in[] = array($args[0] => $args[1]);
		}
		
		return new static;
	}

	/**
	 * Query WHERE OR NOT IN parameter collectors
	 *
	 * @param string $_or_where_not_in
	 * @param string/integer/array
	 * @return new static
	 */
	public static function or_where_not_in()
	{
		$args = func_get_args();
		
		if(count($args) == 2){
			self::$or_where_not_in[] = array($args[0] => $args[1]);
		}
		
		return new static;
	}

	/**
	 * Query LIKE parameter collectors
	 *
	 * @param string $field
	 * @param string/integer $value
	 * @param string $pos
	 * @return new static
	 */
	public static function like($field = false, $value = false, $pos = 'both')
	{
		if($field && $value){
			self::$like[] = array($field => array('val' => $value, 'pos' => $pos));
		}
		
		return new static;
	}

	/**
	 * Query OR LIKE parameter collectors
	 *
	 * @param string $field
	 * @param string/integer $value
	 * @param string $pos
	 * @return new static
	 */
	public static function or_like($field = false, $value = false, $pos = 'both')
	{
		if($field && $value){
			self::$or_like[] = array($field => array('val' => $value, 'pos' => $pos));
		}
		
		return new static;
	}

	/**
	 * Query NOT LIKE parameter collectors
	 *
	 * @param string $field
	 * @param string/integer $value
	 * @param string $pos
	 * @return new static
	 */
	public static function not_like($field = false, $value = false, $pos = 'both')
	{
		if($field && $value){
			self::$not_like[] = array($field => array('val' => $value, 'pos' => $pos));
		}
		
		return new static;
	}

	/**
	 * Query OR NOT LIKE parameter collectors
	 *
	 * @param string $field
	 * @param string/integer $value
	 * @param string $pos
	 * @return new static
	 */
	public static function or_not_like($field = false, $value = false, $pos = 'both')
	{
		if($field && $value){
			self::$or_not_like[] = array($field => array('val' => $value, 'pos' => $pos));
		}
		
		return new static;
	}
	
	public static function group_by($field = false)
	{
		if($field){
			if(is_array($field)){
				foreach($field as $f){
					self::$group_by[] = $f;
				}
			}else{
				self::$group_by[] = $field;
			}
		}
		
		return new static;
	}
	
	public static function distinct()
	{
		self::$distinct = ' DISTINCT ';
		
		return new static;
	}
	
	public static function having()
	{
		$args = func_get_args();
		
		if(count($args) > 0){
			if(count($args) > 1){
				self::$having[] = array($args[0] => $args[1]);
			}else{
				self::$having[] = $args[0];
			}
		}
		
		return new static;
	}
	
	public static function or_having()
	{
		$args = func_get_args();
		
		if(count($args) > 0){
			if(count($args) > 1){
				self::$or_having[] = array($args[0] => $args[1]);
			}else{
				self::$or_having[] = $args[0];
			}
		}
		
		return new static;
	}
	
	public static function order_by()
	{
		$args = func_get_args();
		
		if(count($args) == 2){
			self::$order_by[] = array($args[0] => $args[1]);
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
	
	public static function join($table = false, $compare = false, $type = 'inner')
	{
		preg_match('/(.*?)(\s+)(.*?)/',$table,$matches);
		
		if(count($matches) > 0){
			$table = preg_replace('/' . $matches[0] . '/', self::dbprefix($matches[0]),$table);
		}
		
		self::$join[] = array('table' => $table, 'compare' => $compare, 'type' => $type);
		
		return new static;
	}
	
	public static function get($table = false)
	{
		if(empty(self::$table)){
			self::$table = self::dbprefix($table);
		}
		
		//Exception::test();
		$criterion = self::getCriterion(
						array(
							'select',
							'select_max',
							'select_min',
							'select_avg',
							'select_sum',
							'from' => 'table',
							'join',
							'where',
							'or_where',
							'where_in',
							'or_where_in',
							'where_not_in',
							'or_where_not_in',
							'like',
							'or_like',
							'not_like',
							'or_not_like',
							'limit',
							'offset',
							'group_by',
							'order_by',
							'distinct',
							'having',
							'or_having'
						)
					);
				
		
		self::$query = QR::get($criterion);
		self::execute();
		//var_dump(self::$query);
		return clone new static;
	}

	public static function get_where($table = false, $where = false, $limit = false, $offset = false)
	{
		if($table){
			self::$table = self::dbprefix($table);
			
			self::where($where);
			
			if($limit){
				self::limit($limit);
			}
			
			if($offset){
				self::offset($offset);
			}

			$criterion = self::getCriterion(
						array(
							'select',
							'from' => 'table',
							'where',
							'limit',
							'offset',
						)
					);
					
			self::$query = QR::get($criterion);
			self::execute();
			
			return clone new static;
		}
	}
	
	private static function checkParameterType($table = false, $data = false)
	{
		try {
			if($table){
				if($data && !is_array($data) && !is_object($data)){
					$excParm = array('p' => $data);
					self::$dbErr = ErrorCatcher::errorMsg('incorrect_parm');
					throw new ErrorCatcher(self::$dbErr);
				}else{
					self::$table = self::dbprefix($table);
					return true;
				}
			}else{
				self::$dbErr = ErrorCatcher::errorMsg('table_name');
				throw new ErrorCatcher(self::$dbErr);
			}
		}catch (ErrorCatcher $e) {
			$excParm['e'] = $e;
			echo(ErrorCatcher::fire($excParm));
		}
	}
	
	public static function insert($table = false, $data = false)
	{
		if(self::checkParameterType($table, $data)){
			if(is_object($data)){
				$data = get_object_vars($data);
			}
			
			if($data){
				self::$insert = array('table' => self::$table, 'data' => $data);
			}
			
			if(count(self::$set) > 0){
				if(count(self::$insert) > 0){
					foreach(self::$set as $field => $val){
						self::$insert['data'][$field] = $val;
					}
				}else{
					self::$insert = array('table' => self::$table, 'data' => self::$set);
				}
			}
			
			$criterion = self::getCriterion(array('insert'));
			self::$query = QR::insert($criterion);
			self::execute();
		}
	}
	
	public static function insert_batch($table = false, $data = false)
	{
		if(self::checkParameterType($table, $data)){
			if($data){
				self::$insert_batch = array('table' => self::$table, 'data' => $data);
				$criterion = self::getCriterion(array('insert_batch'));
				self::$query = QR::insert_batch($criterion);
				self::execute();
			}
		}
	}
	
	public static function update($table = false, $data = false, $where = false)
	{
		if(self::checkParameterType($table, $data)){
			if($where){
				self::$where[] = $where;
			}
			
			if(is_object($data)){
				$data = get_object_vars($data);
			}
			
			self::$update = array('table' => self::$table, 'data' => $data);
			
			if(count(self::$set) > 0){
				foreach(self::$set as $field => $val){
					self::$update['data'][$field] = $val;
				}
			}
			
			$criterion = self::getCriterion(
				array(
					'update',
					'table',
					'where',
					'or_where',
					'where_in',
					'or_where_in',
					'where_not_in',
					'or_where_not_in',
					'like',
					'or_like',
					'not_like',
					'or_not_like',
					'limit'
				)
			);
			
			self::$query = QR::update($criterion);
			self::execute();
		}
	}
	
	public static function update_batch($table = false, $data = array(), $refColumn = false)
	{
		try{
			$args = func_get_args();
			$excParm['m'] = __FUNCTION__;
			
			if(count($args) != 3){
				self::$dbErr = ErrorCatcher::errorMsg('update_batch_missing_parameter');
				throw new ErrorCatcher(self::$dbErr);
			}
			
			elseif(!is_string($table)){
				self::$dbErr = ErrorCatcher::errorMsg('table_name');
				throw new ErrorCatcher(self::$dbErr);
			}
			
			elseif(!is_array($data)){
				self::$dbErr = ErrorCatcher::errorMsg('incorrect_parm');
				$excParm['p'] = $data;
				throw new ErrorCatcher(self::$dbErr);
			}
			
			elseif(!$refColumn){
				self::$dbErr = ErrorCatcher::errorMsg('update_batch_ref_col_err');
				throw new ErrorCatcher(self::$dbErr);
			}else{
				self::$table = self::dbprefix($table);
				self::$update_batch = array('table' => self::$table, 'data' => $data, 'ref' => $refColumn);
				
				$criterion = self::getCriterion(array('update_batch'));
				self::$query = QR::update_batch($criterion);
				self::execute();
			}
		}catch(ErrorCatcher $e){
			$excParm['e'] = $e;
			echo(ErrorCatcher::fire($excParm));
		}
	}

	public static function delete($table = false)
	{
		try {
			if(!$table){
				self::$dbErr = ErrorCatcher::errorMsg('table_name');
				throw new ErrorCatcher(self::$dbErr);
			}

			elseif(!is_array($table)){
				$table = array($table);
			}

			foreach($table as $tbl) {
				self::$table = self::dbprefix($tbl);

				$criterion = self::getCriterion(
					array(
						'table',
						'where',
						'or_where',
						'where_in',
						'or_where_in',
						'where_not_in',
						'or_where_not_in',
						'like',
						'or_like',
						'not_like',
						'or_not_like',
						'limit'
					)
				);

				self::$query = QR::delete($criterion);
				self::execute();
			}

		}catch (ErrorCatcher $e){
			$excParm['e'] = $e;
			echo(ErrorCatcher::fire($excParm));
		}
	}

	public static function empty_table($table = false)
	{
		self::delete($table);
	}

	public static function query($sql = null)
	{
		self::$query = $sql;
		self::execute();
		
		return new static;
	}
	
	public static function set()
	{
		$args = func_get_args();
		
		if(count($args) == 2 && !is_array($args[1])){
			self::$set[$args[0]] = $args[1];
		}
		
		elseif(is_object($args[0])){
			self::$set = array_merge(self::$set,get_object_vars($args[0]));
		}
		
		elseif(is_array($args[0])){
			self::$set = array_merge(self::$set,$args[0]);
		}
		
		return new static;
	}
	
	public static function insert_id()
	{
		self::$query = "SELECT LAST_INSERT_ID() as last_insert_id";
		self::execute();
		
		$lastId = self::row()->last_insert_id;
		
		return $lastId == '0' ? null : $lastId;
	}
	
	public static function count_all_results($table = false)
	{
		self::$select[] = 'SQL_CALC_FOUND_ROWS *';
		self::$table = self::dbprefix(!$table ? self::$table : $table);
		
		$criterion = self::getCriterion(
						array(
							'select',
							'from' => 'table',
							'join',
							'where',
							'or_where',
							'where_in',
							'or_where_in',
							'where_not_in',
							'or_where_not_in',
							'like',
							'or_like',
							'not_like',
							'or_not_like'
						)
					);
					
		self::$query = QR::get($criterion);
		self::execute();
		
		self::$query = "SELECT FOUND_ROWS() AS total";
		self::execute();
		
		$result = self::row_array();
		
		return isset($result['total']) ? $result['total'] : 0;
	}
	
	public static function count_all($table = false)
	{
		self::$select[] = 'SQL_CALC_FOUND_ROWS *';
		self::$table = self::dbprefix($table);
		
		$criterion = self::getCriterion(
						array(
							'select',
							'from' => 'table',
						)
					);
					
		self::$query = QR::get($criterion);
		self::execute();
		
		self::$query = "SELECT FOUND_ROWS() AS total";
		self::execute();
		
		$result = self::row_array();
		
		return isset($result['total']) ? $result['total'] : 0;
	}
	
	private static function getCriterion($_criterion)
	{
		$criterion = array();
		
		if(is_array($_criterion)){
			foreach($_criterion as $k => $c){
				
				if(isset(self::$$c) || is_null(self::$$c)){
					$criterion[(!is_numeric($k) ? $k : $c)] = self::$$c;
				}
			}
		}else{
			if(isset(self::$$_criterion) || is_null(self::$$_criterion)){
				$criterion[$_criterion] = self::$$_criterion;
			}
		}
		
		return $criterion;
	}
	
	protected static function execute()
	{
		try {
			self::$dbLink = mysql_connect(self::$dbconf['hostname'], self::$dbconf['username'], self::$dbconf['password']);
		
			if(!self::$dbLink){
				self::$dbErr = 'Check database connections info';
				
				throw new ErrorCatcher(self::$dbErr);
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
				
				throw new ErrorCatcher(self::$dbErr);
			}
		    
		}catch (ErrorCatcher $e) {
			echo(ErrorCatcher::fire(array('e' => $e, 'q' => self::$query)));
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
	
	public static function affected_rows()
	{
		return mysql_affected_rows();
	}
	
	public static function init(&$config)
	{
		self::$dbconf = $config;
	}
	
	protected static function emptySqlVars()
	{
		self::$select = array();
		self::$select_max = '';
		self::$select_min = '';
		self::$select_avg = '';
		self::$select_sum = '';
		self::$where = array();
		self::$or_where = array();
		self::$where_in = array();
		self::$where_not_in = array();
		self::$or_where_not_in = array();
		self::$or_where_in = array();
		self::$like = array();
		self::$or_like = array();
		self::$not_like = array();
		self::$or_not_like = array();
		self::$group_by = array();
		self::$order_by = array();
		self::$having = array();
		self::$or_having = array();
		self::$join = array();
		self::$insert = array();
		self::$insert_batch = array();
		self::$set = array();
		self::$update = array();
		self::$update_batch = array();
		self::$distinct = '';
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