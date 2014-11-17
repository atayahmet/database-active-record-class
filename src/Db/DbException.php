<?php 

namespace Db;

/**
 * @package Database Active Record
 * @author Ahmet ATAY / 2014 November
 * @version 1.0
 *
 * 
 * Error handler class
 * 
 * @contact:
 * 	web: http://www.atayahmet.com
 * 	email: ahmet.atay@hotmail.com
 * 	github: https://github.com/atayahmet
 */

class DbException extends \Exception implements DbExceptionInterface {
	protected static $dbErrMsg = array(
		'incorrect_parm' => 'Incorrect parameter',
		'update_batch_missing_parameter' => 'Missing passed parameters to the update method',
		'update_batch_ref_col_err' => 'Referance colum not specified',
		'table_name' => 'Table name not found',
		'table_name_incorrect' => 'Incorrect table name',
		'config_error' => 'Its database config options is incorrect'
	);
	
	/**
	 * Print error message to browser with template
	 *
	 * @param string $parm
	 * @return string
	 */
	public static function fire($parm = false)
	{
		if(is_array($parm)){
			$view = self::injectView('error', array('ex' => $parm));
			
			return $view;
		}
	}
	
	/**
	 * Error message
	 *
	 * @param string $key
	 * @return string
	 */
	public static function errorMsg($key = false)
	{
		if(isset(self::$dbErrMsg[$key])){
			return self::$dbErrMsg[$key];
		}

		return '';
	}
	
	/**
	 * Inject html source from views folder
	 *
	 * @param string $file
	 * @param array $parm
	 * @return string
	 */
	public static function injectView($file = false, $parm = false)
	{
		ob_start();
		
		if(is_array($parm)){
			foreach($parm as $k => $v) $$k = $v;
		}
		
		htmlentities(include('views/' . $file . '.php'));
		
		return ob_get_clean();
	}
}
