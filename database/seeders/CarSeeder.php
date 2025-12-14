<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Car;

class CarSeeder extends Seeder
{
    public function run(): void
    {
        $cars = [
            [
                'name' => 'Perodua Axia 2024',
                'description' => 'Compact and fuel-efficient city car, perfect for urban driving. Features modern design and comfortable interior.',
                'price_per_day' => 120,
                'exterior_image' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=800&auto=format&fit=crop',
                'interior_image' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&auto=format&fit=crop',
                'features' => ['Air Conditioning', 'Power Steering', 'Bluetooth Audio', 'USB Charging'],
            ],
            [
                'name' => 'Perodua Bezza 2024',
                'description' => 'Spacious sedan with excellent fuel economy. Ideal for long drives and family trips.',
                'price_per_day' => 140,
                'exterior_image' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&auto=format&fit=crop',
                'interior_image' => 'https://images.unsplash.com/photo-1449130015084-2dc954a POcyKQ?w=800&auto=format&fit=crop',
                'features' => ['Air Conditioning', 'Power Windows', 'ABS', 'Airbags', 'Cruise Control'],
            ],
            [
                'name' => 'Perodua Axia 2018',
                'description' => 'Reliable and affordable transportation option. Well-maintained and comfortable.',
                'price_per_day' => 100,
                'exterior_image' => 'https://images.unsplash.com/photo-1583267746897-c27b8382b373?w=800&auto=format&fit=crop',
                'interior_image' => 'https://images.unsplash.com/photo-1485463611174-f302f6a5c1c9?w=800&auto=format&fit=crop',
                'features' => ['Air Conditioning', 'Power Steering', 'Radio'],
            ],
            [
                'name' => 'Perodua Myvi 2015',
                'description' => 'Popular hatchback with great handling. Perfect for city and highway driving.',
                'price_per_day' => 100,
                'exterior_image' => 'https://images.unsplash.com/photo-1502877338535-766e1452684a?w=800&auto=format&fit=crop',
                'interior_image' => 'https://images.unsplash.com/photo-1449130015084-2dc954a4ba67?w=800&auto=format&fit=crop',
                'features' => ['Air Conditioning', 'Power Windows', 'Audio System'],
            ],
            [
                'name' => 'Perodua Myvi 2020',
                'description' => 'Modern and stylish hatchback with advanced safety features and comfortable ride.',
                'price_per_day' => 120,
                'exterior_image' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=800&auto=format&fit=crop',
                'interior_image' => 'https://images.unsplash.com/photo-1542362567-b07e54358753?w=800&auto=format&fit=crop',
                'features' => ['Air Conditioning', 'Touchscreen Display', 'Reverse Camera', 'ABS', 'Airbags'],
            ],
            [
                'name' => 'Hyundai Starex 2020',
                'description' => 'Spacious van perfect for group travel. Comfortable seating for up to 9 passengers.',
                'price_per_day' => 250,
                'exterior_image' => 'https://images.unsplash.com/photo-1527786356703-4b100091cd2c?w=800&auto=format&fit=crop',
                'interior_image' => 'https://images.unsplash.com/photo-1464219789935-c2d9d9aba644?w=800&auto=format&fit=crop',
                'features' => ['Air Conditioning', 'Power Sliding Doors', 'Captain Seats', 'USB Charging', 'Spacious Luggage'],
            ],
        ];

        foreach ($cars as $car) {
            Car::create($car);
        }
    }
}