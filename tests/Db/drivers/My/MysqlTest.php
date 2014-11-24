<?php

namespace tests\Db\drivers\My;

require __DIR__ . '/../../../../../../../vendor/autoload.php';

use \Db\Query as DB;

class MysqlTest extends \PHPUnit_Framework_TestCase {
        private $table = 'members';
        
        /**
        * 
        * @method empty_table
        *
        */
        public function testEmptyTable()
        {
            // tablo sıfırlanıyor
            DB::empty_table($this->table);
            
            // TestCase ile kontrol ediyoruz.
            $this->assertEquals(0, DB::count_all($this->table));
        }
        
        /**
        *
        * @method insert
        *
        */
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
        
        /**
        *
        * @method insert_batch
        *
        * Çoklu kayıt ekleme 
        */
        public function testInsertBatch()
        {
            $total =  DB::count_all($this->table) + 8;
            
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
               ),
               array(
                   'name' => 'Emel',
                   'age' => 24
               ),
               array(
                   'name' => 'Nihal',
                   'age' => 27
               ),
               array(
                   'name' => 'Burcu',
                   'age' => 15
               ),
               array(
                   'name' => 'Sinan',
                   'age' => 26
                )
              )
            );

           $newTotal =  DB::count_all($this->table);

           $this->assertEquals($total, $newTotal);

        }

        /**
        *
        * @method update
        *
        */
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
        
        /**
        *
        * @method update_batch
        *
        * veritabanında toplam da 3 satır etkilenecek şekilde bir güncelleme yapacağız
        * sonra etkilenen satır sayısını TestCase ile kontrol edeceğiz
        *
        */
        public function testUpdateBatch()
        {
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
        
        /**
        *
        * @method delete
        *
        */
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
        
        /**
        *
        * @method select
        *
        */
        public function testSelect()
        {
            // member tablosunda sadece name alanını tanımlıyoruz
            $result = DB::select('name')->from($this->table)->get();
            
            // gelen alanı kontrol ediyoruz
            $this->assertEquals('name', key($result->row_array()));

        }

        /**
        *
        * @method where
        *
        */
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
        
        /**
        *
        * @method or_where
        *
        * or_where metodunun üç ayrı kullanımı test ediliyor 
        *
        */
        public function testOrWhere()
        {
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
        
        /**
        *
        * @method where_in
        *
        * where_in metodu iki farklı kullanımda test ediliyor
        *
        */
        public function testWhereIn()
        {
            // 1. kullanım
            $result = DB::select('*')->from($this->table)->where_in('age', 18)->get();
            $this->assertGreaterThanOrEqual(1, $result->num_rows());
            
            // 2. kullanım
            $result = DB::select('*')->from($this->table)->where_in('name', array(18,21,25));
            $this->assertGreaterThanOrEqual(1, $result->num_rows());
        }
        
        /**
        *
        * @method or_where_in
        *
        */
        public function testOrWhereIn()
        {
            $result = DB::select('*')->from($this->table)->where('name','nothing')->or_where_in('age',18)->get();
            $this->assertGreaterThanOrEqual(1, $result->num_rows());

            $result = DB::select('*')->from($this->table)
                ->where_in('age',18)->or_where_in('age',array(18,21,25))
                ->get();

            $this->assertGreaterThanOrEqual(1, $result->num_rows());
        }

        /**
        * 
        * @method where_not_in
        *
        * İki farklı kullanımda test ediliyor
        *
        */
        public function testWhereNotIn()
        {
            $result = DB::select('*')->from($this->table)->where_not_in('age', 21)->get();
            $this->assertGreaterThan(0, $result->num_rows());

            $result = DB::select('*')->from($this->table)->where_not_in('age', array(21,18))->get();
            $this->assertGreaterThan(0, $result->num_rows());
        }

        /**
        *
        * @method or_where_not_in
        *
        * İki farklı kullanımda test ediliyor
        *
        */
        public function testOrWhereNotIn()
        {
            $result = DB::select('*')->from($this->table)->where_in('age', 30)->or_where_not_in('age', 21)->get();
            $this->assertGreaterThan(0, $result->num_rows());

            $result = DB::select('*')->from($this->table)->where_in('age', 30)->or_where_not_in('age', array(21,18))->get();
            $this->assertGreaterThan(0, $result->num_rows());
        }

        /**
         *
         * @like
         *
         * Tüm varyasyonları test ediliyor
         *
         * */
        public function testLike()
        {
            $result = DB::select('*')->from($this->table)->like('name','Emr')->get();
            $this->assertGreaterThan(0, $result->num_rows());

            $result = DB::select('*')->from($this->table)->like('name','re','before')->get();
            $this->assertGreaterThan(0, $result->num_rows());
            
            $result = DB::select('*')->from($this->table)->like('name','Emr','after')->get();
            $this->assertGreaterThan(0, $result->num_rows());
            
            $result = DB::select('*')->from($this->table)->like(array('name' => 'Emr'))->get();
            $this->assertGreaterThan(0, $result->num_rows());

            $result = DB::select('*')->from($this->table)->like(array('name' => 'Emr'),'after')->get();
            $this->assertGreaterThan(0, $result->num_rows());

             $result = DB::select('*')->from($this->table)->like(array('name' => 're'),'before')->get();
            $this->assertGreaterThan(0, $result->num_rows());
        }

        /**
         *
         * @or_like
         *
         * Tüm varyasyonları test ediliyor
         *
         * */
        public function testOrLike()
        {
            $result = DB::select('*')->from($this->table)->like('name', 'Emr')->or_like('name', 'Ali')->get();
            $this->assertGreaterThan(0, $result->num_rows());

            $result = DB::select('*')->from($this->table)->like('name', 'nothing')->or_like('name', 'li','before')->get();
            $this->assertGreaterThan(0, $result->num_rows());

            $result = DB::select('*')->from($this->table)->like('name', 'nothing')->or_like('name', 'li','Emr','after')->get();
            $this->assertGreaterThan(0, $result->num_rows());

            $result = DB::select('*')->from($this->table)->like('name', 'nothing')->or_like(array('name' => 'Ali'))->get();
            $this->assertGreaterThan(0, $result->num_rows());

            $result = DB::select('*')->from($this->table)->like('name', 'nothing')->or_like(array('name' => 'li'),'before')->get();
            $this->assertGreaterThan(0, $result->num_rows());

            $result = DB::select('*')->from($this->table)->like('name', 'nothing')->or_like(array('name' => 'Emr'),'after')->get();
            $this->assertGreaterThan(0, $result->num_rows());
        }
        
        /**
         *
         * @not_like
         *
         * Tüm varyasyonları test ediliyor
         *
         * */
        public function testNotLike()
        {
            $result = DB::select('*')->from($this->table)->not_like('name','nothing')->get();
            $this->assertGreaterThan(0, $result->num_rows());

            $result = DB::select('*')->from($this->table)->not_like('name','hing','before')->get();
            $this->assertGreaterThan(0, $result->num_rows());

            $result = DB::select('*')->from($this->table)->not_like('name','not','after')->get();
            $this->assertGreaterThan(0, $result->num_rows());

            $result = DB::select('*')->from($this->table)->not_like(array('name' => 'nothing'))->get();
            $this->assertGreaterThan(0, $result->num_rows());

            $result = DB::select('*')->from($this->table)->not_like(array('name' => 'hing'),'before')->get();
            $this->assertGreaterThan(0, $result->num_rows());

            $result = DB::select('*')->from($this->table)->not_like(array('name' => 'not'),'after')->get();
            $this->assertGreaterThan(0, $result->num_rows());
        }
        
        /**
         *
         * @or_not_like
         *
         * Tüm varyasyonları test ediliyor
         *
         * */
        public function testOrNotLike()
        {
            $result = DB::select('*')->from($this->table)->not_like('name','Ali')->or_not_like('name','nothing')->get();
            $this->assertGreaterThan(0, $result->num_rows());

            $result = DB::select('*')->from($this->table)->not_like('name','Ali')->or_not_like('name','hing','before')->get();
            $this->assertGreaterThan(0, $result->num_rows());

            $result = DB::select('*')->from($this->table)->not_like('name','Ali')->or_not_like('name','not','after')->get();
            $this->assertGreaterThan(0, $result->num_rows());

            $result = DB::select('*')->from($this->table)->not_like('name','Ali')->or_not_like(array('name' => 'nothing'))->get();
            $this->assertGreaterThan(0, $result->num_rows());

            $result = DB::select('*')->from($this->table)->not_like('name','Ali')->or_not_like(array('name' => 'hing'),'before')->get();
            $this->assertGreaterThan(0, $result->num_rows());
            
            $result = DB::select('*')->from($this->table)->not_like('name','Ali')->or_not_like(array('name' => 'not'),'after')->get();
            $this->assertGreaterThan(0, $result->num_rows());

        }

        /**
         *
         * @order_by
         *
         * ASC/DESC/RANDOM
         *
         * */
        public function testOrderBy()
        {
            $result1 = DB::select('*')->from($this->table)->order_by('name','asc')->get();
            $data1 = $result1->row()->name;

            $result2 = DB::select('*')->from($this->table)->order_by('name','desc')->get();
            $data2 = $result2->row()->name;

            $this->assertFalse($data1 == $data2);

            $random1 = DB::select('*')->from($this->table)->order_by('name', 'random')->get()->row()->name;
            $random2 = DB::select('*')->from($this->table)->order_by('name', 'random')->get()->row()->name;

            $this->assertFalse($random1 == $random2);
        }
           
        /**
         *
         * @group_by
         *
         * */
        public function testGroupBy()
        {
            $result1 = DB::where('age', 21)->get('members')->num_rows();
            $result2 = DB::where('age', 21)->group_by('age')->get('members')->num_rows();

            $this->assertFalse($result1 == $result2);
        }

        public function testHaving()
        {
            $result1 = DB::group_by('age')->having("age > '18'")->get('members')->row()->age;
            $result2 = DB::group_by('age')->having("age < '19'")->get('members')->row()->age;
            
            $this->assertFalse($result1 == $result2);
        }
        

}


