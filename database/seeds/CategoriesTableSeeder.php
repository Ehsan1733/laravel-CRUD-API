<?php

use App\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->categoriesData() as $category) {
            Category::create($category);
        }
    }

    private function categoriesData()
    {
        return [
            ['name' => 'book'],
            ['name' => 'food'],
            ['name' => 'services'],
        ];
    }
}
