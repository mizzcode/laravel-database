<?php

namespace Tests\Feature;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class QueryBuilderTest extends TestCase
{
    protected function setUp():void {
        parent::setUp();
        DB::delete("delete from products");
        DB::delete("delete from categories");
        DB::delete("delete from counters");
    }

    public function testInsert() {
        DB::table("categories")->insert([
            "id" => "GADGET",
            "name" => "Gadget"
        ]);
        DB::table("categories")->insert([
            "id" => "FOOD",
            "name" => "Food" 
        ]);

        $resultes = DB::select("select count(id) as total from categories");
        self::assertEquals(2, $resultes[0]->total);
    }

    public function testSelect() {
        $this->testInsert();

        $collection = DB::table("categories")->select(["id", "name"])->get();
        self::assertNotNull($collection);

        $collection->each(function($item) {
            Log::info(json_encode($item));
        });
    }

    public function insertCategories() {
        DB::table("categories")->insert([
            "id" => "SMARTPHONE",
            "name" => "Poco M4 Pro",
            "description" => "Mediatek G96 with Panel AMOLED 90Hz",
            "created_at" => "2022-03-16"
        ]);
        DB::table("categories")->insert([
            "id" => "FOOD",
            "name" => "Whiskas",
            "description" => "Makanan Kucing",
            "created_at" => "2021-01-11"
        ]);
        DB::table("categories")->insert([
            "id" => "LAPTOP",
            "name" => "Thinkpad T480",
            "description" => "intel core i5-8350u with 16gb ram",
            "created_at" => "2023-07-05"
        ]);
        DB::table("categories")->insert([
            "id" => "FASHION",
            "name" => "Kemeja",
            "created_at" => "2020-01-11"
        ]);
        DB::table("categories")->insert([
            "id" => "CAR",
            "name" => "When Lambo Sir",
            "created_at" => "2023-05-15"
        ]);
    }

    public function testWhere() {
        $this->insertCategories();

        $collection = DB::table("categories")->where(function(Builder $builder) {
            $builder->where("id", "=", "SMARTPHONE");
            $builder->orWhere("id", "=", "LAPTOP");
            // SELECT * FROM categories WHERE (id = SMARTPHONE OR id = LAPTOP)
        })->get();

        self::assertCount(2, $collection);
        
        $collection->each(function($item) {
            Log::info(json_encode($item));
        });
    }

    public function testWhereBetween() {
        $this->insertCategories();

        $collection = DB::table("categories")->whereBetween("created_at", ["2021-01-10", "2023-08-01"])->get();
        self::assertCount(4, $collection);

        $collection->each(function($item) {
            Log::info(json_encode($item));
        });
    }

    public function testWhereIn() {
        $this->insertCategories();

        $collection = DB::table("categories")->whereIn("id", ["SMARTPHONE", "LAPTOP"])->get();

        self::assertCount(2, $collection);
        
        $collection->each(function($item) {
            Log::info(json_encode($item));
        });
    }
    public function testWhereNull() {
        $this->insertCategories();

        $collection = DB::table("categories")->whereNull("description")->get();

        self::assertCount(2, $collection);
        
        $collection->each(function($item) {
            Log::info(json_encode($item));
        });
    }

    public function testWhereDate() {
        $this->insertCategories();

        $collection = DB::table("categories")->whereDate("created_at", "2020-01-11")->get();

        self::assertCount(1, $collection);
        
        $collection->each(function($item) {
            Log::info(json_encode($item));
        });
    }
    public function testUpdate() {
    
        $this->insertCategories();

        DB::table("categories")->where("id", "=", "CAR" )->update([
            "name" => "Lamborghini"
        ]);

        $collection = DB::table("categories")->where("id", "=", "CAR")->get();

        self::assertCount(1, $collection);

        self::assertEquals("Lamborghini", $collection[0]->name);

        $collection->each(function($item) {
            Log::info(json_encode($item));
        });
    }
    public function testUpdateOrInsert() {
        $this->insertCategories();

        DB::table("categories")->updateOrInsert([
            "id" => "VOUCHER"
        ], [
            "name" => "voucher",
            "description" => "Ticket and Voucher",
            "created_at" => "2023-08-08"
        ]);

        $collection = DB::table("categories")->where("id", "=", "VOUCHER")->get();

        self::assertCount(1, $collection);

        $collection->each(function($item) {
            Log::info(json_encode($item));
        });
    }
    public function testIncrement() {
        DB::table("counters")->insert([
            "id" => "sample",
            "counter" => 0
        ]);

        DB::table("counters")->where("id", "=", "sample")->increment("counter", 1);

        $collection = DB::table("counters")->where("id", "=", "sample")->get();

        self::assertCount(1, $collection);

        $collection->each(function($item) {
            Log::info(json_encode($item));
        });
    }
    public function testDelete() {
        $this->insertCategories();

        DB::table("categories")->where("id", "=", "CAR")->delete();

        $collection = DB::table("counters")->where("id", "=", "CAR")->get();

        self::assertCount(0, $collection);

        $collection->each(function($item) {
            Log::info(json_encode($item));
        });
    }
    public function insertProducts() {
        $this->insertCategories();

        DB::table("products")->insert([
            "id" => "1",
            "name" => "Poco M4 Pro",
            "category_id" => "SMARTPHONE",
            "price" => 2400000
        ]);
        DB::table("products")->insert([
            "id" => "2",
            "name" => "Redmi Note 9",
            "category_id" => "SMARTPHONE",
            "price" => 1900000
        ]);
    }
    public function testJoin() {
        $this->insertProducts();

        $collection = DB::table("products")
        ->join("categories", "products.category_id", "=", "categories.id")
        ->select("products.id", "products.name", "products.price", "categories.id")->get();

        self::assertCount(2, $collection);

        $collection->each(function($item) {
            Log::info(json_encode($item));
        });
    }
    public function testOrdering() {
        $this->insertProducts();

        $collection  =  DB::table("products")->whereNotNull("id")->orderBy("price")->orderBy("name", "desc")->get();

        self::assertCount(2, $collection);

        $collection->each(function($item) {
            Log::info(json_encode($item));
        });
    }
    public function testPaging() {
        $this->insertCategories();

        $collection = DB::table("categories")->skip(2)->take(2)->get();

        self::assertCount(2, $collection);

        $collection->each(function($item) {
            Log::info(json_encode($item));
        });
    }
    public function insertManyCategories() {
        for ($i = 0; $i < 100; $i++) {
            DB::table("categories")->insert([
                "id" => "CATEGORY-$i",
                "name"=> "Category-$i",
                "created_at" => "2023-10-10"
            ]);
        }
    }
    public function testChunk() {
        $this->insertManyCategories();

        DB::table("categories")->orderBy("id")->chunk(10, function($categories) {
            self::assertNotNull($categories);
            Log::info("Start Chunk");
            $categories->each(function($category) {
                Log::info(json_encode($category));
            });
            Log::info("End Chunk");
        });
    }
    public function testLazy() {
        $this->insertManyCategories();

        $collection = DB::table("categories")->orderBy("id")->lazy(10)->take(6);

        self::assertNotNull($collection);

        $collection->each(function($item) {
            Log::info(json_encode($item));
        });
    }
}