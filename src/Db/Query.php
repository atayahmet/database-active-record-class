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
use Db\DbException as ErrorCatcher;

class Query {
	private static $dbconf = null;
	private static $driver;
	private static $dbErrMsg;
	public static function __callStatic($method, $parm)
	{
		self::init(); 
		
		$class = __NAMESPACE__ .'\drivers\\' . substr(self::$driver,0,2) . '\\' . self::$driver;
		
		return call_user_func_array(array($class, $method), $parm);
	}
	
	public static function init($dbConf = false)
	{
		include('/../config.php');
		
		try{
			$current = preg_split('/\:/',$current);
			
			if(count($current) < 2|| !isset($db[$current[0]][$current[1]])){
				self::$dbErrMsg = ErrorCatcher::errorMsg('config_error');
				throw new ErrorCatcher(self::$dbErrMsg);
			}
		}catch(ErrorCatcher $e){
			$excParm['e'] = $e;
			echo(ErrorCatcher::fire($excParm));
		}
		
		self::$driver = $current[0];
		$currentConf = $db[$current[0]][$current[1]];
		
		if(!$dbConf){
			if(is_null(self::$dbconf )){
				self::$dbconf = $currentConf;
				self::$driver = ucfirst(self::$driver);
				
				$_driver = 'Db\drivers\My\\' . self::$driver;
				$_driver::init($currentConf);
			}
		}else{
			self::$dbconf = $config;
		}
	}
}
