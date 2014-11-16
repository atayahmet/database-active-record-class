<?php namespace Db;

class DbException extends \Exception implements DbExceptionInterface {
	protected static $dbErrMsg = array(
		'incorrect_parm' => 'Incorrect parameter',
		'update_batch_missing_parameter' => 'Missing passed parameters to the update method',
		'update_batch_ref_col_err' => 'Referance colum not specified',
		'table_name' => 'Table name not found',
		'table_name_incorrect' => 'Incorrect table name'
	);

	public static function fire($parm = false)
	{
		if(is_array($parm)){
			$view = self::injectView('error', array('ex' => $parm));
			
			return $view;
		}
	}

	public static function errorMsg($key = false)
	{
		if(isset(self::$dbErrMsg[$key])){
			return self::$dbErrMsg[$key];
		}

		return '';
	}

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
