<?php

namespace Db\drivers\My;

/**
 * @package Database Active Record
 * @author Ahmet ATAY / 2014 November
 * @version 1.0
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
 * 
 * @contact:
 * 	web: http://www.atayahmet.com
 * 	email: ahmet.atay@hotmail.com
 * 	github: https://github.com/atayahmet
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

	// SELECT SUM()
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
        
        protected static $affected_rows = 0;
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
	
	/**
	 * Query GROUP BY parameter collectors
	 *
	 * @param string/array $field
	 * @return new static
	 */
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
	
	/**
	 * Query DISTINCT parameter
	 *
	 * @return new static
	 */
	public static function distinct()
	{
		self::$distinct = ' DISTINCT ';
		
		return new static;
	}
	
	/**
	 * Query HAVING parameter collectors
	 *
	 * @param string/array $field
	 * @param string/array $field
	 * @return new static
	 */
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
	
	/**
	 * Query HAVING OR parameter collectors
	 *
	 * @param string/array $field
	 * @param string/array $field
	 * @return new static
	 */
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
	
	/**
	 * Query ORDER parameter collectors
	 *
	 * @param string/array $field
	 * @param ASC/DESC/RANDOM $field
	 * @return new static
	 */
	public static function order_by()
	{
		$args = func_get_args();
		
		if(count($args) == 2){
			self::$order_by[] = array($args[0] => $args[1]);
		}
		
		return new static;
	}
	
	/**
	 * Query table decisive
	 *
	 * @param string $table
	 * @return new static
	 */
	public static function from($table = null)
	{
		self::$table = self::dbprefix($table);
		
		return new static;
	}
	
	/**
	 * Query LIMIT features
	 *
	 * @param integer $_limit
	 * @param integer $_offset for skip row (optional)
	 * @return new static
	 */
	public static function limit($_limit = null, $_offset = null)
	{
		self::$limit = $_limit;
		
		if(!is_null($_offset)){
			self::$offset = $_offset;
		}
		
		return new static;
	}
	
	/**
	 * For skip data
	 *
	 * @param integer $_offset for skip row
	 * @return new static
	 */
	public static function offset($_offset = null)
	{
		self::$offset = $_offset;
		
		return new static;
	}
	
	/**
	 * parameter collectors for JOIN actions
	 *
	 * @param string $table
	 * @param string $compare
	 * @param string $type (optional)
	 * @return new static
	 */
	public static function join($table = false, $compare = false, $type = 'inner')
	{
		preg_match('/(.*?)(\s+)(.*?)/',$table,$matches);
		
		if(count($matches) > 0){
			$table = preg_replace('/' . $matches[0] . '/', self::dbprefix($matches[0]),$table);
		}
		
		self::$join[] = array('table' => $table, 'compare' => $compare, 'type' => $type);
		
		return new static;
	}
	
	/**
	 * Will run select queries
	 *
	 * @param string/array $table
	 * @return new static
	 */
	public static function get($table = false)
	{
		if(empty(self::$table)){
			self::$table = self::dbprefix($table);
		}
		
		//-----------------------------------------------------------------------
		//
		// All parameters of the query is compiled and sent to the query builder
		//
		//-----------------------------------------------------------------------
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
				
		// query building
		self::$query = QR::get($criterion);
		
		// and execute
		self::execute();
		
		return clone new static;
	}
	
	/**
	 * will make of the get and the where methods
	 *
	 * @param string $table
	 * @param string/array $where
	 * @param integer $limit (optional)
	 * @param integer $offset (optional)
	 * @return new static
	 */
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
			
			//---------------------------------
			// compiling of the query parameter
			//---------------------------------
			$criterion = self::getCriterion(
						array(
							'select',
							'from' => 'table',
							'where',
							'limit',
							'offset',
						)
					);
			
			// query building
			self::$query = QR::get($criterion);
			
			// and execute
			self::execute();
			
			return clone new static;
		}
	}
	
	/**
	 * Control parameters
	 *
	 * @type internal method
	 * @param string $table
	 * @param array $data
	 * @return bool
	 */
	private static function checkParameterType($table = false, $data = false)
	{
		//--------------------------------------------------------
		// Some parameters must be array in query 
		// Here we will check the array of parameters that need to be
		// Criteria will throw error is not appropriate
		//--------------------------------------------------------
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
	
	/**
	 * Will run insert queries
	 *
	 * @param string $table
	 * @param array $data
	 */
	public static function insert($table = false, $data = false)
	{
		// control parameters type
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
			
			//---------------------------------
			// compiling of the query parameter
			//---------------------------------
			$criterion = self::getCriterion(array('insert'));
			
			// query building
			self::$query = QR::insert($criterion);
			
			// and execute
			self::execute();
		}
	}
	
	/**
	 * Will run insert multi-data to database. it's run the single query 
	 *
	 * @param string $table
	 * @param array $data
	 * @return bool
	 */
	public static function insert_batch($table = false, $data = false)
	{
		// control parameters type
		if(self::checkParameterType($table, $data)){
			if($data){
				self::$insert_batch = array('table' => self::$table, 'data' => $data);
				$criterion = self::getCriterion(array('insert_batch'));
				self::$query = QR::insert_batch($criterion);
				self::execute();
			}
		}
	}
	
	/**
	 * Will run update queries
	 *
	 * @param string $table
	 * @param array $data
	 * @param string/array $where (optional)
	 * @return void
	 */
	public static function update($table = false, $data = false, $where = false)
	{
		// control parameters type
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
			
			//---------------------------------
			// compiling of the query parameter
			//---------------------------------
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
			
			// query building
			self::$query = QR::update($criterion);
			
			// and execute
			self::execute();
		}
	}
	
	/**
	 * Will run update multi-data at database. it's run the single query 
	 *
	 * @param string $table
	 * @param array $data
	 * @param string $refColumn
	 * @return void
	 */
	public static function update_batch($table = false, $data = array(), $refColumn = false)
	{
		//--------------------------------------------------------------------
		// Used sql case for multi update
		// You will find that this is done driver/My/QueryCreator class method
		// -------------------------------------------------------------------
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
	
	/**
	 * Will run delete query 
	 *
	 * @param string $table
	 * @return void
	 */
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
				
				//---------------------------------
				// compiling of the query parameter
				//---------------------------------
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
				
				// query building
				self::$query = QR::delete($criterion);
				
				// and execute
				self::execute();
			}

		}catch (ErrorCatcher $e){
			$excParm['e'] = $e;
			echo(ErrorCatcher::fire($excParm));
		}
	}
	
	/**
	 * All data will delete from table
	 *
	 * @param string $table
	 * @return void
	 */
	public static function empty_table($table = false)
	{
		self::delete($table);
	}
	
	/**
	 * will run native query
	 *
	 * @param string $sql
	 * @return new static
	 */
	public static function query($sql = null)
	{
		self::$query = $sql;
		self::execute();
		
		return new static;
	}
	
	/**
	 * collect data for update
	 *
	 * @param string $arg1
	 * @param string/integer $arg2
	 * @param array $arg1
	 * @return new static
	 */
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
	/**
	 * Will take the last insert id
	 *
	 * @return integer
	 */
	public static function insert_id()
	{
		self::$query = "SELECT LAST_INSERT_ID() as last_insert_id";
		self::execute();
		
		$lastId = self::row()->last_insert_id;
		
		return $lastId == '0' ? null : $lastId;
	}
	
	/**
	 * Data counter with the where criteria
	 *
	 * @param sitring $table
	 * @return integer
	 */
	public static function count_all_results($table = false)
	{
		self::$select[] = 'SQL_CALC_FOUND_ROWS *';
		self::$table = self::dbprefix(!$table ? self::$table : $table);
		
		//---------------------------------
		// compiling of the query parameter
		//---------------------------------
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
					
		// query building
		self::$query = QR::get($criterion);
		
		// and execute
		self::execute();
		
		self::$query = "SELECT FOUND_ROWS() AS total";
		self::execute();
		
		$result = self::row_array();
		
		return isset($result['total']) ? $result['total'] : 0;
	}
	
	/**
	 * Will count all data  without the where criteria
	 *
	 * @param string $table
	 * @return integer
	 */
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
	
	/**
	 * Create all query criteria
	 *
	 * @type internal method
	 * @param string/array $_criterion
	 * @return array
	 */
	private static function getCriterion($_criterion)
	{
		//----------------------------------------------
		// We will compile sql parameters in this method
		// ---------------------------------------------
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
	
	/**
	 * executes the query
	 *
	 * @type internal method
	 * @return void
	 */
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
		        
                        self::$affected_rows = mysql_affected_rows();

                        self::emptySqlVars();
		}catch (ErrorCatcher $e) {
			echo(ErrorCatcher::fire(array('e' => $e, 'q' => self::$query)));
		}
	}

	/**
	 * get the query dump
	 *
	 * @param string $type
	 * @return array
	 */
	public static function dump($type= 'html')
	{
		switch($type){
			case "html";
				return ErrorCatcher::injectView('query_dump',array('queries' => self::$Queries));
				break;

			case "array";
				return self::$Queries;
				break;
		}

	}

	/**
	 * Create prefixes  with the table name
	 *
	 * @param string $table
	 * @return void
	 */
	public static function dbprefix($table = null)
	{
		if(!is_null($table) && preg_match('/' . preg_quote(self::$dbconf['dbprefix']) . '(.*?)/', $table) < 1){
			return self::$dbconf['dbprefix'] . $table;
		}
		
		return $table;
	}
	
	/**
	 * its total rows access  of the results
	 *
	 * @return integer
	 */
	public static function num_rows()
	{
                self::emptySqlVars();

		return mysql_num_rows(self::$qResult);
	}
	
	/**
	 * It's access with sort id at query results
	 *
	 * @param integer $num
	 * @return object
	 */
	public static function row($num = 0)
	{
		$result = self::result();
		
		if(isset($result->{$num})){
			return $result->{$num};
		}
	}
	
	/**
	 * It's access with sort id at query results
	 *
	 * @param integer $num
	 * @return array
	 */
	public static function row_array($num = 0)
	{
		$result = self::result_array();
		
		if(isset($result[$num])){
			return $result[$num];
		}
	}
	
	/**
	 * Results get as object
	 *
	 * @return object
	 */
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
	
	/**
	 * Results get as array
	 *
	 * @return array
	 */
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
	
	/**
	 * Take affected row count
	 *
	 * @return integer
	 */
	public static function affected_rows()
	{
		return mysql_affected_rows();
	}
	
	/**
	 * Generate database connections
	 * 
	 * @param array $config
	 * @return void
	 */
	public static function init(&$config)
	{
		self::$dbconf = $config;
	}
	
	/**
	 * All variables reset for new query
	 * 
	 * @return void
	 */
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
                self::$affected_rows = 0;
		self::$table = '';
	}
	
	/**
	 * Close database connection
	 * 
	 * @return void
	 */
	protected static function dbConnectionClose()
	{
		if(!self::$dbClosed){
			self::$dbClosed = true;
			mysql_close(self::$dbLink);
		}
	}
}
