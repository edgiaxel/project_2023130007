<?php

// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Costume; // <-- ADD THIS
use App\Models\Order; // <-- ADD THIS
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. CREATE ROLES AND USERS (FIXED: Using firstOrCreate)
        // This prevents the RoleAlreadyExists error if you run db:seed multiple times.
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'renter']);
        Role::firstOrCreate(['name' => 'user']);

        // Similarly, update the User creation to prevent duplicate users
        $admin = User::firstOrCreate(['email' => 'admin@starium.test'], ['name' => 'Admin Boss', 'password' => bcrypt('password')]);
        $admin->assignRole('admin');

        $renter = User::firstOrCreate(['email' => 'renter@starium.test'], ['name' => 'Renter Provider', 'password' => bcrypt('password')]);
        $renter->assignRole('renter');

        $user = User::firstOrCreate(['email' => 'user@starium.test'], ['name' => 'Regular User', 'password' => bcrypt('password')]);
        $user->assignRole('user');

        // 2. CREATE DUMMY COSTUMES (Owned by Renter Provider)
        $costumesData = [
            ['name' => 'Sailor Moon Uniform', 'series' => 'Sailor Moon', 'price' => 50000, 'tags' => ['Magical Girl', 'Anime']],
            ['name' => 'Genshin Impact (Focalors)', 'series' => 'Genshin Impact', 'price' => 85000, 'tags' => ['Game', 'Fantasy']],
            ['name' => 'Jedi Knight Robes', 'series' => 'Star Wars', 'price' => 60000, 'tags' => ['Sci-Fi', 'Movie']],
            ['name' => 'Naruto Hokage Outfit', 'series' => 'Naruto', 'price' => 70000, 'tags' => ['Anime', 'Ninja']],
            ['name' => 'Alphonse Elric Armor', 'series' => 'Fullmetal Alchemist', 'price' => 75000, 'tags' => ['Armor', 'Anime']],
        ];

        foreach ($costumesData as $data) {
            $costume = Costume::firstOrCreate(
                ['name' => $data['name']],
                [
                    'user_id' => $renter->id,
                    'series' => $data['series'],
                    'size' => 'M',
                    'condition' => 'Excellent',
                    'price_per_day' => $data['price'],
                    'stock' => 1,
                    'main_image_path' => 'default.jpg', // Placeholder
                    'is_approved' => true,
                    'tags' => $data['tags'],
                ]
            );
        }

        // 3. CREATE DUMMY ORDERS (Placed by Regular User)
        $jediRobes = Costume::where('name', 'Jedi Knight Robes')->first();
        $sailorMoon = Costume::where('name', 'Sailor Moon Uniform')->first();

        if ($jediRobes && $sailorMoon) {
            // Active Order (Borrowed)
            Order::firstOrCreate(
                ['order_code' => 'ORD-2025001'],
                [
                    'costume_id' => $jediRobes->id,
                    'user_id' => $user->id,
                    'start_date' => now()->subDays(2),
                    'end_date' => now()->addDays(3),
                    'total_price' => $jediRobes->price_per_day * 5, // 5 days
                    'status' => 'borrowed',
                ]
            );

            // Pending Order (Waiting)
            Order::firstOrCreate(
                ['order_code' => 'ORD-2025002'],
                [
                    'costume_id' => $sailorMoon->id,
                    'user_id' => $user->id,
                    'start_date' => now()->addDays(10),
                    'end_date' => now()->addDays(12),
                    'total_price' => $sailorMoon->price_per_day * 2, // 2 days
                    'status' => 'waiting',
                ]
            );
        }
    }
}