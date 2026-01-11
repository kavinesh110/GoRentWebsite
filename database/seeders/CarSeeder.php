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
                'plate_number' => 'JHQ1234',
                'brand' => 'Perodua',
                'model' => 'Axia',
                'fuel_type' => 'petrol',
                'year' => 2024,
                'base_rate_per_hour' => 5.00,
                'status' => 'available',
                'current_mileage' => 15000,
                'service_mileage_limit' => 20000,
                'last_service_date' => '2025-11-01',
                'image_url' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=800&auto=format&fit=crop',
                'gps_enabled' => true,
            ],
            [
                'plate_number' => 'JHQ5678',
                'brand' => 'Perodua',
                'model' => 'Bezza',
                'fuel_type' => 'petrol',
                'year' => 2024,
                'base_rate_per_hour' => 5.83,
                'status' => 'available',
                'current_mileage' => 12000,
                'service_mileage_limit' => 20000,
                'last_service_date' => '2025-10-15',
                'image_url' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&auto=format&fit=crop',
                'gps_enabled' => true,
            ],
            [
                'plate_number' => 'JHQ9012',
                'brand' => 'Perodua',
                'model' => 'Axia',
                'fuel_type' => 'petrol',
                'year' => 2018,
                'base_rate_per_hour' => 4.17,
                'status' => 'available',
                'current_mileage' => 45000,
                'service_mileage_limit' => 50000,
                'last_service_date' => '2025-09-20',
                'image_url' => 'https://images.unsplash.com/photo-1583267746897-c27b8382b373?w=800&auto=format&fit=crop',
                'gps_enabled' => false,
            ],
            [
                'plate_number' => 'JHQ3456',
                'brand' => 'Perodua',
                'model' => 'Myvi',
                'fuel_type' => 'petrol',
                'year' => 2015,
                'base_rate_per_hour' => 4.17,
                'status' => 'available',
                'current_mileage' => 60000,
                'service_mileage_limit' => 70000,
                'last_service_date' => '2025-08-10',
                'image_url' => 'https://images.unsplash.com/photo-1502877338535-766e1452684a?w=800&auto=format&fit=crop',
                'gps_enabled' => false,
            ],
            [
                'plate_number' => 'JHQ7890',
                'brand' => 'Perodua',
                'model' => 'Myvi',
                'fuel_type' => 'petrol',
                'year' => 2020,
                'base_rate_per_hour' => 5.00,
                'status' => 'available',
                'current_mileage' => 30000,
                'service_mileage_limit' => 40000,
                'last_service_date' => '2025-10-01',
                'image_url' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=800&auto=format&fit=crop',
                'gps_enabled' => true,
            ],
            [
                'plate_number' => 'JHQ2468',
                'brand' => 'Hyundai',
                'model' => 'Starex',
                'fuel_type' => 'diesel',
                'year' => 2020,
                'base_rate_per_hour' => 10.42,
                'status' => 'available',
                'current_mileage' => 25000,
                'service_mileage_limit' => 30000,
                'last_service_date' => '2025-11-15',
                'image_url' => 'https://images.unsplash.com/photo-1527786356703-4b100091cd2c?w=800&auto=format&fit=crop',
                'gps_enabled' => true,
            ],
        ];

        foreach ($cars as $car) {
            // Only create if car with this plate number doesn't exist
            Car::firstOrCreate(
                ['plate_number' => $car['plate_number']],
                $car
            );
        }
    }
}
