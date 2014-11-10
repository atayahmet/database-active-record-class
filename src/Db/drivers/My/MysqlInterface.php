<?php namespace Db\drivers\My;

Interface MysqlInterface
{
    public function __construct();
    public static function select($_select = null);
    public static function where($_where = null);
	public static function where_in();
	public static function or_where_in();
	public static function where_not_in();
	public static function or_where_not_in();
	public static function or_where($_where = null);
	public static function limit($_limit = null, $_offset = null);
	public static function offset($_offset);
	public static function num_rows();
	public static function dbprefix($table = null);
	public static function init(&$config);
	public static function row($num);
	public static function row_array($num);
	public static function result();
	public static function result_array();
}