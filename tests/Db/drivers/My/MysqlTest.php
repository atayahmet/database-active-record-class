<?php

namespace Db;

use \Db\Query as DB;

class MysqlTest extends \PHPUnit_Framework_TestCase {
	public function testInsert()
	{
		// ilk senaryomuz tabloya yeni kayıt oluşturma
		// kullanıcı tablosuna bir yeni üye ekliyoruz ve ardın etkilenen kayıt sayısını
		// UnitTest'e gönderiyoruz
		DB::insert('members',
			array(
			'name' 	=> 'Ahmet',
			'age'	=> 18
			)
		);

		// sonuç istediğimiz gibimi kontrol ediyoruz.
		$this->assertGreaterThan(0, DB::insert_id());
	}

	public function testUpdate()
	{
		// ikinci senaryomuz update metodu.
		// Tablodaki ilk kaydı guncelleyeceğiz.
		// Sonrasında tekrar etkilenen satır sayısı alacağız.
		$result = DB::select('*')->order_by('id','asc')->get('members');
		var_dump($result->result());
	
	}
}
