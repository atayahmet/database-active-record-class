<?php namespace Db;

class DbException extends \Exception implements DbExceptionInterface {
	public static function fire($parm = false)
	{
		if(is_array($parm)){
			$view = self::injectView('error', array('ex' => $parm));
			
			return $view;
		}
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
