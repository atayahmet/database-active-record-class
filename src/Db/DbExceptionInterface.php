<?php

namespace Db;

interface DbExceptionInterface {
	public static function fire($e);
	public static function injectView($file = false);
}
