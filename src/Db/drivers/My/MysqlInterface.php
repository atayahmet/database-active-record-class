<?php namespace Db\drivers\My;

Interface MysqlInterface
{
    public static function select($_select = null);
	public static function select_max($field = false);
	public static function select_min($field = false);
	public static function select_avg($field = false);
	public static function select_sum($field = false);
    public static function where($_where = null);
	public static function where_in();
	public static function or_where_in();
	public static function where_not_in();
	public static function or_where_not_in();
	public static function or_where($_where = null);
	public static function like($field = false, $value = false, $pos = 'both');
	public static function or_like($field = false, $value = false, $pos = 'both');
	public static function not_like($field = false, $value = false, $pos = 'both');
	public static function or_not_like($field = false, $value = false, $pos = 'both');
	public static function group_by($field = false);
	public static function distinct();
	public static function having();
	public static function or_having();
	public static function order_by();
	public static function limit($_limit = null, $_offset = null);
	public static function offset($_offset);
	public static function num_rows();
	public static function dbprefix($table = null);
	public static function row($num);
	public static function row_array($num);
	public static function result();
	public static function result_array();
	public static function count_all_results($table = false);
	public static function count_all($table = false);
	public static function join($table = false, $compare = false, $type = 'inner join');
	public static function get_where($table = false, $where = false, $limit = false, $offset = false);
}