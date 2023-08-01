<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RawQueryTest extends TestCase
{
    protected function setUp():void {
        parent::setUp();
        DB::delete("DELETE FROM categories");
    }

    public function testCrud() {
        DB::insert("INSERT INTO categories(id, name, description, created_at) values (?,?,?,?)", [
            "PS", "PlayStation-2", "PlayStation 2 by Sony", "2003-12-03 00:00:00"
        ]) ;

        $result = DB::select("SELECT * FROM categories where id = ?", [
            "PS"
        ]);

        self::assertCount(1, $result);
        self::assertEquals("PS", $result[0]->id);
        self::assertEquals("PlayStation-2", $result[0]->name);
        self::assertEquals("PlayStation 2 by Sony", $result[0]->description);
        self::assertEquals("2003-12-03 00:00:00", $result[0]->created_at);
    }
}
