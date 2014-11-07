<?php namespace Db\drivers\My;

Interface MysqlInterface
{
    public function __construct();
    public static function select($_select = false);
    public static function where();
	public static function init(&$config);
	public static function row();
	public static function result();
	public static function result_array();
}