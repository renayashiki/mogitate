<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 季節と商品のシーダーを呼び出す
        $this->call([
            SeasonSeeder::class, 
            ProductSeeder::class, 
        ]);
    }
}
