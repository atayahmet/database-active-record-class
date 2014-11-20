<?php

namespace Db;

include __DIR__ . '/../../../../../../../vendor/autoload.php';
use \Db\Query as DB;

class MysqlTest extends \PHPUnit_Framework_TestCase {
	public function testAdd()
	{
		var_dump(DB::get('langs'));
	}
}
