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
        DB::delete("delete from categories");
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
}