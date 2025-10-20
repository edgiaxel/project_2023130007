<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Costume;
use App\Models\Order;
use App\Models\RenterStore;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Carbon\Carbon; // Added for orders

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- 1. SETUP ROLES AND BASE USERS ---
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'renter']);
        Role::firstOrCreate(['name' => 'user']);

        $FILE_PATHS = [
            'admin_avatar' => 'user_profiles/3.png',
            'user_avatar' => 'user_profiles/2.png',
            'renter1_avatar' => 'user_profiles/4.png',
            'renter2_avatar' => 'user_profiles/5.png',
            'renter3_avatar' => 'user_profiles/1.png',
        ];

        User::firstOrCreate(['email' => 'admin@starium.test'], [
            'name' => 'Admin Boss',
            'password' => bcrypt('password'),
            'profile_picture' => $FILE_PATHS['admin_avatar'],
        ])->assignRole('admin');

        $user = User::firstOrCreate(['email' => 'user@starium.test'], [
            'name' => 'Regular User',
            'password' => bcrypt('password'),
            'profile_picture' => $FILE_PATHS['user_avatar'],
        ])->assignRole('user');


        // --- 2. CREATE MULTIPLE RENTER USERS & STORES ---
        $rentersData = [
            [
                'user' => [
                    'name' => 'Captain Cosmic',
                    'email' => 'renter1@starium.test',
                    'phone_number' => '0811-1234-5678',
                    'address' => 'Andromeda Galaxy Hub 1',
                    'profile_picture' => 'user_profiles/4.png',
                ],
                'store' => [
                    'name' => 'Cosmic Threads',
                    'description' => 'The largest collection of space and sci-fi costumes!',
                    'logo' => 'store_logos/cosmicthreads.png',
                ],
                'costumes' => [
                    ['name' => 'Star Lord Helmet Jacket', 'series' => 'Guardians of Galaxy', 'price' => 120000, 'tags' => ['Movie', 'Star Lord', 'Sci-Fi']],
                    ['name' => 'Gundam Pilot Suit', 'series' => 'Mobile Suit Gundam', 'price' => 95000, 'tags' => ['Anime', 'Gundam', 'Mecha']],
                    ['name' => 'Flash Speedster Suit', 'series' => 'DC Comics', 'price' => 110000, 'tags' => ['Movie', 'Flash', 'Superhero']],
                    ['name' => 'Alien Xenomorph Suit', 'series' => 'Alien', 'price' => 150000, 'tags' => ['Movie', 'Xenomorph', 'Horror']],
                    ['name' => 'Jedi Knight Robes', 'series' => 'Star Wars', 'price' => 60000, 'tags' => ['Movie', 'Jedi', 'Fantasy']],
                    ['name' => 'Master Chief Armor', 'series' => 'Halo', 'price' => 130000, 'tags' => ['Game', 'Master Chief', 'Armor']],
                    ['name' => 'EVA Unit-01 Plugsuit', 'series' => 'Evangelion', 'price' => 105000, 'tags' => ['Anime', 'Shinji', 'Mecha']],
                ],
            ],
            [
                'user' => [
                    'name' => 'Princess Aurora',
                    'email' => 'renter2@starium.test',
                    'phone_number' => '0822-9876-5432',
                    'address' => 'Nebula Cluster HQ 7',
                    'profile_picture' => 'user_profiles/5.png',
                ],
                'store' => [
                    'name' => 'Fairy Dust Rentals',
                    'description' => 'Fantasy, magic, and royal attire for all your event needs.',
                    'logo' => 'store_logos/fairydustrentals.png',
                ],
                'costumes' => [
                    ['name' => 'Sailor Moon Uniform', 'series' => 'Sailor Moon', 'price' => 50000, 'tags' => ['Anime', 'Sailor Moon', 'Magical Girl']],
                    ['name' => 'Elsa Ice Dress', 'series' => 'Frozen', 'price' => 70000, 'tags' => ['Movie', 'Elsa', 'Princess']],
                    ['name' => 'Genshin Impact (Focalors)', 'series' => 'Genshin Impact', 'price' => 85000, 'tags' => ['Game', 'Focalors', 'Fantasy']],
                    ['name' => 'Witcher Geralt Armor', 'series' => 'The Witcher', 'price' => 90000, 'tags' => ['Game', 'Geralt', 'Fantasy']],
                    ['name' => 'Cinderella Ball Gown', 'series' => 'Cinderella', 'price' => 80000, 'tags' => ['Movie', 'Cinderella', 'Princess']],
                    ['name' => 'T-Rex Kigurumi', 'series' => 'Jurassic Park', 'price' => 45000, 'tags' => ['Other', 'T-Rex', 'Funny']], // Changed series to give it a media context
                    ['name' => 'Daenerys Targaryen Gown', 'series' => 'Game of Thrones', 'price' => 115000, 'tags' => ['TV', 'Daenerys', 'Fantasy']],
                ],
            ],
            [
                'user' => [
                    'name' => 'The Anime King',
                    'email' => 'renter3@starium.test',
                    'phone_number' => '0833-1122-3344',
                    'address' => 'Tokyo-3 Outpost 5',
                    'profile_picture' => 'user_profiles/1.png',
                ],
                'store' => [
                    'name' => 'Weeb Central',
                    'description' => 'The hottest anime and manga threads in the Milky Way!',
                    'logo' => 'store_logos/weebcentral.png',
                ],
                'costumes' => [
                    ['name' => 'Naruto Hokage Outfit', 'series' => 'Naruto', 'price' => 70000, 'tags' => ['Anime', 'Naruto', 'Ninja']],
                    ['name' => 'Attack on Titan Uniform', 'series' => 'Attack on Titan', 'price' => 60000, 'tags' => ['Anime', 'Eren', 'Military']],
                    ['name' => 'Demon Slayer Tanjiro Kimono', 'series' => 'Demon Slayer', 'price' => 65000, 'tags' => ['Anime', 'Tanjiro', 'Kimono']],
                    ['name' => 'One Piece Luffy Gear 5', 'series' => 'One Piece', 'price' => 125000, 'tags' => ['Anime', 'Luffy', 'Shonen']],
                    ['name' => 'Kirito Black Swordsman', 'series' => 'Sword Art Online', 'price' => 75000, 'tags' => ['Anime', 'Kirito', 'Game']],
                    ['name' => 'Fullmetal Alchemist Uniform', 'series' => 'Fullmetal Alchemist', 'price' => 80000, 'tags' => ['Anime', 'Edward', 'Military']],
                    ['name' => 'Lelouch Lamperouge Zero', 'series' => 'Code Geass', 'price' => 100000, 'tags' => ['Anime', 'Lelouch', 'Uniform']],
                ],
            ],
        ];

        foreach ($rentersData as $data) {
            $renter = User::firstOrCreate(
                ['email' => $data['user']['email']],
                array_merge($data['user'], ['password' => bcrypt('password')])
            );
            $renter->assignRole('renter');

            RenterStore::firstOrCreate(
                ['user_id' => $renter->id],
                [
                    'store_name' => $data['store']['name'],
                    'description' => $data['store']['description'],
                    'store_logo_path' => $data['store']['logo'],
                ]
            );

            foreach ($data['costumes'] as $costumeData) {
                $imageName = 'costumes/' . str_replace([' ', '&', '(', ')'], ['_', '', '', ''], strtolower($costumeData['name'])) . '.jpg';

                Costume::firstOrCreate(
                    ['name' => $costumeData['name']],
                    [
                        'user_id' => $renter->id,
                        'series' => $costumeData['series'],
                        'size' => 'M',
                        'condition' => 'Excellent',
                        'price_per_day' => $costumeData['price'],
                        'stock' => 1,
                        'main_image_path' => $imageName,
                        'is_approved' => true,
                        'tags' => $costumeData['tags'],
                    ]
                );
            }
        }

        // --- 3. CREATE PENDING COSTUMES FOR APPROVAL ---
        $pendingCostumesData = [
            ['user_id' => 3, 'name' => 'Deadpool Suit', 'series' => 'Marvel', 'price' => 150000, 'tags' => ['Movie', 'Deadpool', 'Anti-Hero']],
            ['user_id' => 3, 'name' => 'Space Marine Armor', 'series' => 'Warhammer 40k', 'price' => 200000, 'tags' => ['Game', 'Space Marine', 'Armor']],

            ['user_id' => 4, 'name' => 'Alice in Wonderland Dress', 'series' => 'Disney', 'price' => 70000, 'tags' => ['Movie', 'Alice', 'Fantasy']],
            ['user_id' => 4, 'name' => 'Belle Ball Gown', 'series' => 'Beauty and the Beast', 'price' => 90000, 'tags' => ['Movie', 'Belle', 'Princess']],
            
            ['user_id' => 5, 'name' => 'Ichigo Bankai', 'series' => 'Bleach', 'price' => 110000, 'tags' => ['Anime', 'Ichigo', 'Shonen']],
            ['user_id' => 5, 'name' => 'Goku Ultra Instinct', 'series' => 'Dragon Ball', 'price' => 130000, 'tags' => ['Anime', 'Goku', 'Shonen']],
        ];

        foreach ($pendingCostumesData as $costumeData) {
            $imageName = 'costumes/' . str_replace([' ', '&', '(', ')'], ['_', '', '', ''], strtolower($costumeData['name'])) . '_pending.jpg'; // Different image name for pending items
            
            Costume::firstOrCreate(['name' => $costumeData['name']], [
                'user_id' => $costumeData['user_id'],
                'series' => $costumeData['series'],
                'size' => 'M',
                'condition' => 'New',
                'price_per_day' => $costumeData['price'],
                'stock' => 1,
                'main_image_path' => $imageName,
                'is_approved' => false,
                'tags' => $costumeData['tags'],
            ]);
        }

        // --- 3. CREATE DUMMY ORDERS ---
        $statuses = ['waiting', 'confirmed', 'borrowed', 'returned', 'completed', 'rejected'];
        $orderCounter = 1;
        $customer = User::where('email', 'user@starium.test')->first();

        if ($customer) {
            $renters = User::role('renter')->get();

            foreach ($renters as $renter) {
                $costumes = $renter->costumes;
                if ($costumes->isEmpty()) {
                    continue;
                }

                foreach ($statuses as $status) {
                    $costume = $costumes->get(($orderCounter - 1) % $costumes->count());

                    // Define dates based on status
                    switch ($status) {
                        case 'completed': 
                            $start = Carbon::now()->subMonths(1)->startOfMonth()->subDays(5);
                            $end = $start->copy()->addDays(5);
                            break;
                        case 'returned': 
                            $start = Carbon::now()->subWeeks(1)->subDays(3);
                            $end = $start->copy()->addDays(7);
                            break;
                        case 'borrowed':
                            $start = Carbon::now()->subDays(2);
                            $end = Carbon::now()->addDays(5);
                            break;
                        case 'confirmed': 
                            $start = Carbon::now()->addDays(5);
                            $end = $start->copy()->addDays(3);
                            break;
                        case 'waiting': 
                            $start = Carbon::now()->addDays(15);
                            $end = $start->copy()->addDays(2);
                            break;
                        case 'rejected': 
                            $start = Carbon::now()->subMonths(2);
                            $end = $start->copy()->addDays(4);
                            break;
                        default:
                            $start = Carbon::now();
                            $end = $start->copy()->addDays(3);
                    }

                    $duration = $start->diffInDays($end) + 1;
                    $totalPrice = $costume->price_per_day * $duration;

                    Order::firstOrCreate(['order_code' => 'ORD-' . str_pad($orderCounter, 3, '0', STR_PAD_LEFT)], [
                        'costume_id' => $costume->id,
                        'user_id' => $customer->id,
                        'start_date' => $start,
                        'end_date' => $end,
                        'total_price' => $totalPrice,
                        'status' => $status,
                    ]);

                    $orderCounter++; 
                } 
            }
        }

        $this->call(BannerSeeder::class);
    }
}