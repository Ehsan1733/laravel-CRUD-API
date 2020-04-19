<?php

use App\Brand;
use Illuminate\Database\Seeder;

class BrandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->brandsData() as $brand) {
            Brand::create($brand);
        }
    }

    private function brandsData()
    {
        return [
            [
                'name' => 'Brand name No 1',
                'description' => 'Brand Description No 1',
                'category_id' => 1,
            ],
            [
                'name' => 'Brand name No 2',
                'description' => 'Brand Description No. 2',
                'category_id' => 2,
            ],
            [
                'name' => 'Brand name No 3',
                'description' => 'Brand Description No 3',
                'category_id' => 3,
            ],
        ];
    }
}
