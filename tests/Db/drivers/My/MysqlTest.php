<?php

namespace tests;

require __DIR__ . '/../../../../../../../vendor/autoload.php';

use \Db\Query as DB;

class MysqlTest extends \PHPUnit_Framework_TestCase {

	public function testInsert()
	{
		// ilk senaryomuz tabloya yeni kayıt oluşturma
		// kullan¿c¿ tablosuna bir yeni üye ekliyoruz ve ard¿ndan etkilenen kay¿t say¿s¿n¿
		// UnitTest'e gönderiyoruz
		DB::insert('members',
			array(
			'name' 	=> 'Ahmet',
			'age'	=> 18
			)
		);

		// sonuç istedi¿imiz gibimi kontrol ediyoruz.
		$this->assertGreaterThan(0, DB::insert_id());
	}

	public function testUpdate()
	{
		// ikinci senaryomuz update metodu.
		// Tablodaki ilk kayd¿ guncelleyece¿iz.
		// Sonras¿nda tekrar etkilenen sat¿r say¿s¿ alaca¿¿z.
		$result = DB::select('id')->limit(1)->order_by('id','random')->get('members');
		
		// gelen satır sayısı bir olmalı
		$this->assertEquals(1, $result->num_rows());
		
		// update metodunu kullanıp test'e hazırlıyoruz
		DB::where('id', $result->row()->id)
			->update('members', array('name' => 'Serdar'));
		
		// etkilenen satır sayısı TestCase'e gönderiliyor
		$this->assertGreaterThan(0,DB::affected_rows());
	
	}
}

