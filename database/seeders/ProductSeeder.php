<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create categories
        $categories = [
            ['name' => 'Men', 'slug' => 'men'],
            ['name' => 'Women', 'slug' => 'women'],
            ['name' => 'Footwear', 'slug' => 'footwear'],
            ['name' => 'Accessories', 'slug' => 'accessories'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        // Create sample products
        $menCategory = Category::where('slug', 'men')->first();
        $womenCategory = Category::where('slug', 'women')->first();
        
        if ($menCategory) {
            Product::updateOrCreate(['slug' => 'mens-t-shirt'], [
                'category_id' => $menCategory->category_id,
                'name' => "Men's Classic T-Shirt",
                'slug' => 'mens-t-shirt',
                'description' => 'Comfortable cotton t-shirt for everyday wear.',
                'price' => 2500.00,
                'stock' => 50,
                'is_active' => true,
                'is_featured' => true,
            ]);
        }

        if ($womenCategory) {
            Product::updateOrCreate(['slug' => 'womens-dress'], [
                'category_id' => $womenCategory->category_id,
                'name' => "Women's Summer Dress",
                'slug' => 'womens-dress',
                'description' => 'Elegant summer dress perfect for any occasion.',
                'price' => 4500.00,
                'stock' => 25,
                'is_active' => true,
                'is_featured' => false,
            ]);
        }
    }
}