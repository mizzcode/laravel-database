<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
}
