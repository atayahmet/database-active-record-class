<?php 

namespace Db;

/**
 * @package Database Active Record
 * @author Ahmet ATAY / 2014 November
 * @version 1.0
 *
 * 
 * Container class
 * 
 * @contact:
 * 	web: http://www.atayahmet.com
 * 	email: ahmet.atay@hotmail.com
 * 	github: https://github.com/atayahmet
 */
 
use Db\drivers\My\Mysql;

class Query {
	private static $dbconf = null;
	private static $driver;
	
	public static function __callStatic($method, $parm)
	{
		self::init(); 
		
		$class = __NAMESPACE__ .'\drivers\\' . substr(self::$driver,0,2) . '\\' . self::$driver;
		
		return call_user_func_array(array($class, $method), $parm);
	}
	
	public static function init($dbConf = false)
	{
		include(APPPATH.'config/database'.EXT);
		
		if(!$dbConf){
			if(is_null(self::$dbconf )){
				self::$dbconf = $db;
				//self::$active = $active_group;
				
				self::$driver = ucfirst($db[$active_group]['dbdriver']);
				
				$_driver = 'Db\drivers\My\\' . self::$driver;
				
				$_driver::init($db[$active_group]);
			}
		}else{
			self::$dbconf = $config;
		}
	}
}
