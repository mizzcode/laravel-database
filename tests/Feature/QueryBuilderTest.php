<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
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
}
