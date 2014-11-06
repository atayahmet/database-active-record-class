<?php namespace Db\drivers\My;

Interface MysqlInterface
{
    public function __construct();
    public static function select($_select = false);
    public static function where();
	public static function connect($config);
}