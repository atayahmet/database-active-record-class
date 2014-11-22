<?php

namespace tests;

require __DIR__ . '/../../../../../../../vendor/autoload.php';

use \Db\Query as DB;

class MysqlTest extends \PHPUnit_Framework_TestCase {
        public $table = 'members';

        public function testEmptyTable()
        {
            // tablo sıfırlanıyor
            DB::empty_table($this->table);
            
            // TestCase ile kontrol ediyoruz.
            $this->assertEquals(0, DB::count_all($this->table));
        }

	public function testInsert()
	{
            DB::empty_table($this->table);

	    // ilk senaryomuz tabloya yeni kayıt oluşturma
	    // kullan¿c¿ tablosuna bir yeni üye ekliyoruz ve ard¿ndan etkilenen kay¿t say¿s¿n¿
	    // UnitTest'e gönderiyoruz
	    DB::insert($this->table,
	        array(
		  'name' => 'Ahmet',
		  'age'  => 18
	        )
	    );

	    // sonuç istedi¿imiz gibimi kontrol ediyoruz.
	    $this->assertGreaterThan(0, DB::insert_id());
		
	}
        
        public function testInsertBatch()
        {
            $total =  DB::count_all($this->table) + 4;
            
            DB::insert_batch($this->table, array(
                array(
                   'name' => 'Sibel',
                   'age'  => 18
                ),
                array(
                   'name' => 'Selim',
                   'age' => 21
                ),
                array(
                   'name' => 'Emre',
                   'age' => 20
                ),
                array(
                   'name' => 'Ali',
                   'age' => 22
                )
              )
            );

           $newTotal =  DB::count_all($this->table);

           $this->assertEquals($total, $newTotal);

        }
	public function testUpdate()
	{
		// ikinci senaryomuz update metodu.
		// Tablodaki ilk kayd¿ guncelleyece¿iz.
		// Sonras¿nda tekrar etkilenen sat¿r say¿s¿ alaca¿¿z.
		$result = DB::select('id,name')->where('name','Selim')->limit(1)->get($this->table);
		
		// gelen satır sayısı bir olmalı
		$this->assertEquals(1, $result->num_rows());
		
		// update metodunu kullanıp test'e hazırlıyoruz
		DB::where('id', $result->row()->id)->update($this->table, array('name' => 'Selim Emre'));
		
		// etkilenen satır sayısı TestCase'e gönderiliyor
		$this->assertGreaterThan(0, DB::affected_rows());	
	}

        public function testUpdateBatch()
        {
            // veritabanında 3 tane satırın değerini değiştireceğiz
            // sonra etkilenen satır sayısını TestCase ile kontrol edeceğiz
            $data = array(
                array(
                  'name'  => 'Ali',
                  'age'   => 25
                ),

                array(
                  'name'  => 'Ahmet',
                  'age'   => 19
                ),

                array(
                  'name'  => 'Emre',
                  'age'   => 21
                )
            );
            
            // güncelliyoruz
            DB::update_batch($this->table, $data, 'name');
            
            // kontrol ediyoruz
            $this->assertEquals(3,DB::affected_rows());
        }

        public function testDelete()
        {
            // genel toplamı alıp sonuçtan bir çıkarıyoruz
            $total = DB::count_all($this->table) - 1;
            
            // tablodan bir kişiyi siliyoruz
            DB::where('name', 'Ahmet')->limit(1)->delete($this->table);
            
            // tekrar genel toplamı alıyoruz
            $newTotal = DB::count_all($this->table);
            
            // sonuçları kontrol ediyoruz
            $this->assertEquals($total, $newTotal);
        }

        public function testSelect()
        {
            // member tablosunda sadece name alanını tanımlıyoruz
            $result = DB::select('name')->from($this->table)->get();
            
            // gelen alanı kontrol ediyoruz
            $this->assertEquals('name', key($result->row_array()));

        }

        public function testWhere()
        {
            // ilk olarak boş sonuç dönecek bir sorgu gönderiyoruz
            $result = DB::select('*')->from($this->table)->where('name', 'nothing')->get();
            $this->assertFalse($result->num_rows() > 0);
            
            // sonrasında farklı varyoslarla aynı sorguyu çalıştırıyoruz
            // varyasyon 1
            $result = DB::select('*')->from($this->table)->where('name','Ali')->get();
            $this->assertGreaterThanOrEqual(1, $result->num_rows());
            
            // varyasyon 2
            $result = DB::select('*')->from($this->table)->where(array('name' => 'Ali'))->get();
            $this->assertGreaterThanOrEqual(1, $result->num_rows());
            
            // varyasyon 3
            $result = DB::select('*')->from($this->table)->where("name = 'Ali'")->get();
            $this->assertGreaterThanOrEqual(1, $result->num_rows());

        }

        public function testOrWhere()
        {
            // or_where metodunun 3 ayrı kullanımı test ediliyor
            // değerleri iki ayrı parametre olarak gönderiliyor
            $result = DB::select('*')->from($this->table)->where('name', 'nothing')->or_where('name', 'Ali')->get();
            $this->assertGreaterThanOrEqual(1, $result->num_rows());
            
            // parametreler bir dizi içinde gönderiliyor
            $result = DB::select('*')->from($this->table)->where(array('name' => 'nothing'))->or_where(array('name' => 'Ali'))->get();
            $this->assertGreaterThanOrEqual(1, $result->num_rows());
            
            // native sql cümlesi olarak gönderiliyor
            $result = DB::select('*')->from($this->table)->where("name = 'nothing'")->or_where("name = 'Ali'")->get();
            $this->assertGreaterThanOrEqual(1, $result->num_rows());
        }

        public function testWhereIn()
        {
            $result = DB::select('*')->from($this->table)->where_in('age', 18)->get();
            $this->assertGreaterThanOrEqual(1, $result->num_rows());
        }
}


