<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
}
