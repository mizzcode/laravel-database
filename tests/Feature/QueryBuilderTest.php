<?php

namespace Tests\Feature;

use Database\Seeders\CategorySeeder;
use Database\Seeders\CounterSeeder;
use Database\Seeders\ProductSeeder;
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
        $this->seed(CategorySeeder::class);
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
        $this->seed(CounterSeeder::class);

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

        $this->seed(ProductSeeder::class);
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
    public function testCursor() {
        $this->insertManyCategories();

        // cursor menggunakan PDO::fetch untuk mengambil data 1 per 1 berbeda dengan chunk atau lazy
        $collection = DB::table("categories")->orderBy("id")->cursor();

        self::assertNotNull($collection);

        $collection->each(function($item) {
            Log::info(json_encode($item));
        });
    }
    public function testAggregate() {
        $this->insertProducts();

        $count = DB::table("products")->count("id");
        self::assertEquals(2, $count);

        $min = DB::table("products")->min("price");
        self::assertEquals(1900000, $min);

        $max = DB::table("products")->max("price");
        self::assertEquals(2400000, $max);

        $avg = DB::table("products")->avg("price");
        $result = ($min + $max) / 2;
        self::assertEquals($result, $avg);
        Log::info("Rata-rata Harga Produk : " . $result);

        $sum = DB::table("products")->sum("price");
        $total = $min + $max;
        self::assertEquals($total, $sum);
        Log::info("Total Harga Produk : " . $total);
    }

    public function testQueryBuilderRaw() {
        $this->insertProducts();

        $collection = DB::table("products")->select(
            DB::raw("count(id) as total_product"),
            DB::raw("min(price) as min_price"),
            DB::raw("max(price) as max_price"),
        )->get();

        self::assertEquals(2, $collection[0]->total_product);
    }

    public function insertProductsFood() {
        DB::table("products")->insert([
            "id" => "3",
            "name" => "Whiskas",
            "price" => 15000,
            "category_id" => "FOOD"
        ]);
        DB::table("products")->insert([
            "id" => "4",
            "name" => "Mie Ayam Bakso",
            "price" => 9000,
            "category_id" => "FOOD"
        ]);
    }

    public function testGroupByHaving() {
        $this->insertProducts();
        $this->insertProductsFood();

        $collection = DB::table("products")
        ->select("category_id", DB::raw("count(*) as total_product"))
        ->groupBy("category_id")
        ->orderBy("category_id", "desc")
        ->get();

        self::assertCount(2, $collection);
        self::assertEquals("SMARTPHONE", $collection[0]->category_id);
        self::assertEquals("FOOD", $collection[1]->category_id);
        self::assertEquals(2, $collection[0]->total_product);
        self::assertEquals(2, $collection[1]->total_product);

        $collection->each(function($item) {
            Log::info(json_encode($item));
        });
    }

    public function testLocking() {
        $this->insertProducts();

        DB::transaction(function() {
            $collection = DB::table("products")
            ->where("id", "=", "1")
            ->lockForUpdate()
            ->get();

            self::assertEquals("Poco M4 Pro", $collection[0]->name);
        });
    }

    public function testPagination() {
        $this->insertCategories();

        $paginate = DB::table("categories")->paginate(perPage: 2, page: 2);

        self::assertEquals(2, $paginate->currentPage());
        self::assertEquals(2, $paginate->perPage());
        self::assertEquals(3, $paginate->lastPage());
        self::assertEquals(5, $paginate->total());

        $collection = $paginate->items();

        // page 1 itu 2 item
        self::assertCount(2, $collection);

        foreach ($collection as $item) {
            Log::info(json_encode($item));
        }
    }

    public function testIteratePerPage() {
        $this->insertCategories();

        $page = 1;

        while (true) {
            $paginate = DB::table("categories")->paginate(perPage: 2, page: $page);

            if ($paginate->isEmpty()) {
                break;
            } else {
                $page++;

                $collection = $paginate->items();

                self::assertNotNull($collection);

                foreach ($collection as $item) {
                    Log::info(json_encode($item));
                }
            }
        }
    }

    public function testCursorPagination() {
        $this->insertCategories();

        $cursor = "id";

        while(true) {
            $paginate = DB::table("categories")->orderBy("id")->cursorPaginate(perPage: 2, cursor: $cursor);

            foreach ($paginate->items() as $item) {
                self::assertNotNull($item);
                Log::info(json_encode($item));
            }

            $cursor = $paginate->nextCursor();
            if ($cursor === null) {
                break;
            }
        }
    }
}