<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Only create test user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]
        );

        // Seed cars with new schema (only creates if they don't exist)
        // $this->call(CarSeeder::class);
        
        // Seed dummy data (customers, bookings, maintenance, etc.)
        // NOTE: DummyDataSeeder ONLY uses existing cars - it does NOT create new cars
        $this->call(DummyDataSeeder::class);
        
        // Fix existing data to match realistic business logic
        // Uncomment the line below to fix existing bookings and cancellation requests:
        // $this->call(FixExistingDataSeeder::class);
    }
}
