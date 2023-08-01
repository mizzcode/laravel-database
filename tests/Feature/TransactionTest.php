<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    protected function setUp():void {
        parent::setUp();
        DB::delete("delete from categories");
    }

    public function testTransactionSuccess() {
        DB::transaction(function() {
            DB::insert("insert into categories(id,name,description,created_at) values (?,?,?,?)", [
                "FOOD", "Food Cat", "Food Cat Whiskas", "2020-12-12 20:20:20"
            ]);
        });

        $results = DB::select("select * from categories where id = ?", [
            "FOOD"
        ]);

        self::assertCount(1,  $results);
    }
    public function testTransactionFailed() {
        
        try {
            DB::transaction(function() {
                DB::insert("insert into categories(id,name,description,created_at) values (?,?,?,?)", [
                    "FOOD", "Food Cat", "Food Cat Whiskas", "2020-12-12 20:20:20"
                ]);
                DB::insert("insert into categories(id,name,description,created_at) values (?,?,?,?)", [
                    "FOOD", "Food Cat", "Food Cat Whiskas", "2020-12-12 20:20:20"
                ]);
            });
        } catch(QueryException) {

        }

        $results = DB::select("select * from categories where id = ?", [
            "FOOD"
        ]);

        self::assertCount(0,  $results);
    }
}
