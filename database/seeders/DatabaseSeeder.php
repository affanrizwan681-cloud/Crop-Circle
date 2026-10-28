<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Announcement;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Conversation;
use App\Models\FarmerProfile;
use App\Models\Favorite;
use App\Models\Market;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PickupSlot;
use App\Models\Product;
use App\Models\ProductAvailability;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Initial Administrator
        $admin = User::firstOrCreate(
            ['email' => 'admin@marketlink.com'],
            [
                'name' => 'Platform Administrator',
                'phone' => '+1 (555) 010-0001',
                'address' => 'CropCircle HQ, 100 Agri-Commerce Way',
                'role' => 'admin',
                'status' => 'active',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Verified Farmers
        $farmer1 = User::firstOrCreate(
            ['email' => 'farmer@marketlink.com'],
            [
                'name' => 'Thomas Green',
                'phone' => '+1 (555) 234-5678',
                'address' => '245 Orchard Valley Road, Springfield',
                'role' => 'farmer',
                'status' => 'active',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $profile1 = FarmerProfile::firstOrCreate(
            ['user_id' => $farmer1->id],
            [
                'farm_name' => 'Green Pastures Organic Farm',
                'contact_person' => 'Thomas Green',
                'description' => 'A 4th-generation certified organic farm producing heritage vegetables, orchard fruits, and herbs without synthetic pesticides or chemical fertilizers.',
                'address' => '245 Orchard Valley Road',
                'city' => 'Springfield',
                'state' => 'IL',
                'postal_code' => '62701',
                'latitude' => 39.7817,
                'longitude' => -89.6501,
                'operating_days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                'pickup_start_time' => '08:00:00',
                'pickup_end_time' => '17:00:00',
                'is_approved' => true,
            ]
        );

        $farmer2 = User::firstOrCreate(
            ['email' => 'sarah.grower@marketlink.com'],
            [
                'name' => 'Sarah Jenkins',
                'phone' => '+1 (555) 345-6789',
                'address' => '88 Berry Hill Trail, Riverdale',
                'role' => 'farmer',
                'status' => 'active',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $profile2 = FarmerProfile::firstOrCreate(
            ['user_id' => $farmer2->id],
            [
                'farm_name' => 'Highland Berry & Produce Orchard',
                'contact_person' => 'Sarah Jenkins',
                'description' => 'Fresh seasonal berries, stone fruits, culinary herbs, and crisp greens freshly harvested each morning.',
                'address' => '88 Berry Hill Trail',
                'city' => 'Riverdale',
                'state' => 'IL',
                'postal_code' => '62704',
                'latitude' => 39.7990,
                'longitude' => -89.6440,
                'operating_days' => ['Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                'pickup_start_time' => '09:00:00',
                'pickup_end_time' => '16:00:00',
                'is_approved' => true,
            ]
        );

        $farmer3 = User::firstOrCreate(
            ['email' => 'david.dairy@marketlink.com'],
            [
                'name' => 'David Miller',
                'phone' => '+1 (555) 456-7890',
                'address' => '120 Clover Meadow Lane, Oakville',
                'role' => 'farmer',
                'status' => 'active',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $profile3 = FarmerProfile::firstOrCreate(
            ['user_id' => $farmer3->id],
            [
                'farm_name' => 'Heritage Valley Dairy & Apiary',
                'contact_person' => 'David Miller',
                'description' => 'Small batch grass-fed dairy products, raw honey, free-range chicken eggs, and artisan cheeses crafted on farm.',
                'address' => '120 Clover Meadow Lane',
                'city' => 'Oakville',
                'state' => 'IL',
                'postal_code' => '62707',
                'latitude' => 39.8100,
                'longitude' => -89.6300,
                'operating_days' => ['Tuesday', 'Thursday', 'Saturday'],
                'pickup_start_time' => '08:30:00',
                'pickup_end_time' => '15:00:00',
                'is_approved' => true,
            ]
        );

        // 3. Pending Farmer (for testing admin moderation queue)
        $farmerPending = User::firstOrCreate(
            ['email' => 'new.farmer@marketlink.com'],
            [
                'name' => 'Lucas Vance',
                'phone' => '+1 (555) 567-8901',
                'address' => '77 Sunrise Ridge Road, Pine Creek',
                'role' => 'farmer',
                'status' => 'pending',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        FarmerProfile::firstOrCreate(
            ['user_id' => $farmerPending->id],
            [
                'farm_name' => 'Sunrise Eco Farmstead',
                'contact_person' => 'Lucas Vance',
                'description' => 'Family permaculture homestead seeking to distribute organic heirloom microgreens and mushroom kits.',
                'address' => '77 Sunrise Ridge Road',
                'city' => 'Pine Creek',
                'state' => 'IL',
                'postal_code' => '62711',
                'latitude' => 39.8250,
                'longitude' => -89.6200,
                'operating_days' => ['Saturday', 'Sunday'],
                'pickup_start_time' => '10:00:00',
                'pickup_end_time' => '14:00:00',
                'is_approved' => false,
            ]
        );

        // 4. Customer Accounts
        $customer1 = User::firstOrCreate(
            ['email' => 'customer@marketlink.com'],
            [
                'name' => 'John Doe',
                'phone' => '+1 (555) 987-6543',
                'address' => '512 Elm Street, Apt 3B, Springfield',
                'role' => 'customer',
                'status' => 'active',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $customer2 = User::firstOrCreate(
            ['email' => 'emma.shopper@marketlink.com'],
            [
                'name' => 'Emma Watson',
                'phone' => '+1 (555) 876-5432',
                'address' => '742 Evergreen Terrace, Springfield',
                'role' => 'customer',
                'status' => 'active',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        // 5. Markets
        $market1 = Market::firstOrCreate(
            ['name' => 'Springfield Historic Farmers Market'],
            [
                'location' => 'Springfield Downtown Plaza',
                'address' => '100 Market Square, Springfield, IL 62701',
                'description' => 'The regional hub for community agriculture featuring 40+ local growers, bakers, and artisans every Saturday and Sunday morning.',
                'latitude' => 39.7817,
                'longitude' => -89.6501,
                'operating_days' => ['Saturday', 'Sunday'],
                'open_time' => '07:30:00',
                'close_time' => '13:00:00',
                'status' => 'active',
            ]
        );

        $market2 = Market::firstOrCreate(
            ['name' => 'Valley Green Riverside Market'],
            [
                'location' => 'Riverfront Park Pavilions',
                'address' => '450 Riverside Drive, Riverdale, IL 62704',
                'description' => 'A scenic open-air market connecting suburban families with regenerative farmers and fresh daily harvests.',
                'latitude' => 39.7990,
                'longitude' => -89.6440,
                'operating_days' => ['Wednesday', 'Saturday'],
                'open_time' => '08:00:00',
                'close_time' => '14:00:00',
                'status' => 'active',
            ]
        );

        $market3 = Market::firstOrCreate(
            ['name' => 'Oakville Community Farmers Fair'],
            [
                'location' => 'Town Green Square',
                'address' => '12 Pine Street, Oakville, IL 62707',
                'description' => 'Mid-week evening market dedicated to fresh dairy, baked artisan breads, organic poultry, and seasonal pantry items.',
                'latitude' => 39.8100,
                'longitude' => -89.6300,
                'operating_days' => ['Tuesday', 'Friday'],
                'open_time' => '08:00:00',
                'close_time' => '13:30:00',
                'status' => 'active',
            ]
        );

        // Farmer-Market associations
        $profile1->markets()->syncWithoutDetaching([
            $market1->id => ['assigned_stall' => 'Stall #14 (Central Pavilion)', 'status' => 'approved'],
            $market2->id => ['assigned_stall' => 'Stall #08 (Riverside Walk)', 'status' => 'approved'],
        ]);

        $profile2->markets()->syncWithoutDetaching([
            $market1->id => ['assigned_stall' => 'Stall #22 (Berry Row)', 'status' => 'approved'],
            $market3->id => ['assigned_stall' => 'Stall #05 (Green Pavilions)', 'status' => 'approved'],
        ]);

        $profile3->markets()->syncWithoutDetaching([
            $market2->id => ['assigned_stall' => 'Stall #11 (Dairy Corner)', 'status' => 'approved'],
            $market3->id => ['assigned_stall' => 'Stall #02 (Honey & Dairy)', 'status' => 'approved'],
        ]);

        // 6. Categories
        $categoriesData = [
            ['name' => 'Vegetables', 'slug' => 'vegetables', 'icon' => 'bi-flower2', 'description' => 'Freshly picked leafy greens, root vegetables, squash, and heritage tubers.'],
            ['name' => 'Fruits', 'slug' => 'fruits', 'icon' => 'bi-apple', 'description' => 'Crisp tree fruit, orchard stone fruits, and sweet berries ripened on the vine.'],
            ['name' => 'Dairy & Eggs', 'slug' => 'dairy-eggs', 'icon' => 'bi-egg-fried', 'description' => 'Farm-fresh milk, artisan goat & cow cheeses, and free-range pastured eggs.'],
            ['name' => 'Honey & Preserves', 'slug' => 'honey-preserves', 'icon' => 'bi-droplet-half', 'description' => 'Raw wildflower honey, small-batch berry jams, and fruit preserves.'],
            ['name' => 'Herbs & Microgreens', 'slug' => 'herbs-microgreens', 'icon' => 'bi-flower1', 'description' => 'Aromatic kitchen herbs, culinary seasoning bunches, and nutrient-dense microgreens.'],
            ['name' => 'Plants & Seedlings', 'slug' => 'plants-seedlings', 'icon' => 'bi-tree', 'description' => 'Heirloom vegetable starters, potted perennials, and pollinator plants.'],
            ['name' => 'Grains & Cereals', 'slug' => 'grains-cereals', 'icon' => 'bi-boxes', 'description' => 'Stone-ground heritage flours, rolled oats, and organic culinary grains.'],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['slug']] = Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 7. Products
        $productsData = [
            [
                'farmer_id' => $farmer1->id,
                'category_id' => $categories['vegetables']->id,
                'name' => 'Heirloom Rainbow Carrots',
                'slug' => 'heirloom-rainbow-carrots',
                'description' => 'Sweet, crunchy purple, yellow, and orange heritage carrots pulled from fertile organic soil within 24 hours of pickup.',
                'price' => 3.80,
                'unit' => 'bunch',
                'stock_quantity' => 45,
                'weekly_quota' => 60,
                'status' => 'available',
                'is_featured' => true,
            ],
            [
                'farmer_id' => $farmer1->id,
                'category_id' => $categories['vegetables']->id,
                'name' => 'Organic Vine-Ripened Heirloom Tomatoes',
                'slug' => 'organic-vine-ripened-heirloom-tomatoes',
                'description' => 'Juicy multi-colored Brandywine and Cherokee Purple tomatoes bursting with rich, old-fashioned summer flavor.',
                'price' => 4.50,
                'unit' => 'kg',
                'stock_quantity' => 35,
                'weekly_quota' => 50,
                'status' => 'available',
                'is_featured' => true,
            ],
            [
                'farmer_id' => $farmer1->id,
                'category_id' => $categories['herbs-microgreens']->id,
                'name' => 'Sweet Genovese Garden Basil',
                'slug' => 'sweet-genovese-garden-basil',
                'description' => 'Fragrant, tender Italian basil grown outdoors with sunshine. Perfect for homemade pesto and fresh caprese salads.',
                'price' => 2.25,
                'unit' => 'bunch',
                'stock_quantity' => 28,
                'weekly_quota' => 40,
                'status' => 'available',
                'is_featured' => false,
            ],
            [
                'farmer_id' => $farmer2->id,
                'category_id' => $categories['fruits']->id,
                'name' => 'Crisp Honeycrisp Apples',
                'slug' => 'crisp-honeycrisp-apples',
                'description' => 'Tree-ripened Honeycrisp apples with an explosive sweet-tart crunch. Grown in our sun-drenched highland orchard.',
                'price' => 4.90,
                'unit' => 'kg',
                'stock_quantity' => 60,
                'weekly_quota' => 80,
                'status' => 'available',
                'is_featured' => true,
            ],
            [
                'farmer_id' => $farmer2->id,
                'category_id' => $categories['fruits']->id,
                'name' => 'Sweet Alpine Strawberries',
                'slug' => 'sweet-alpine-strawberries',
                'description' => 'Hand-picked ruby red strawberries picked at peak sweetness. Packed in recyclable pint baskets.',
                'price' => 5.50,
                'unit' => 'basket',
                'stock_quantity' => 30,
                'weekly_quota' => 45,
                'status' => 'available',
                'is_featured' => true,
            ],
            [
                'farmer_id' => $farmer2->id,
                'category_id' => $categories['honey-preserves']->id,
                'name' => 'Wild Blackberry Orchard Jam',
                'slug' => 'wild-blackberry-orchard-jam',
                'description' => 'Hand-crafted small batch preserve containing only wild blackberries, organic cane sugar, and lemon juice.',
                'price' => 7.50,
                'unit' => 'jar',
                'stock_quantity' => 20,
                'weekly_quota' => 30,
                'status' => 'available',
                'is_featured' => false,
            ],
            [
                'farmer_id' => $farmer3->id,
                'category_id' => $categories['dairy-eggs']->id,
                'name' => 'Artisan Grass-Fed Farmhouse Cheddar',
                'slug' => 'artisan-grass-fed-farmhouse-cheddar',
                'description' => 'Aged 12 months raw milk cheddar with sharp, crumbly texture and rich buttercup golden paste.',
                'price' => 9.20,
                'unit' => 'piece (250g)',
                'stock_quantity' => 25,
                'weekly_quota' => 35,
                'status' => 'available',
                'is_featured' => true,
            ],
            [
                'farmer_id' => $farmer3->id,
                'category_id' => $categories['dairy-eggs']->id,
                'name' => 'Pasture-Raised Brown Hen Eggs (Dozen)',
                'slug' => 'pasture-raised-brown-hen-eggs',
                'description' => 'Dozen large brown eggs from hens free to roam green clover pastures every single day. Deep golden orange yolks.',
                'price' => 6.00,
                'unit' => 'dozen',
                'stock_quantity' => 40,
                'weekly_quota' => 60,
                'status' => 'available',
                'is_featured' => true,
            ],
            [
                'farmer_id' => $farmer3->id,
                'category_id' => $categories['honey-preserves']->id,
                'name' => 'Raw Prairie Wildflower Honey',
                'slug' => 'raw-prairie-wildflower-honey',
                'description' => 'Unpasteurized, unfiltered 100% pure raw honey harvested from our wildflower bee yards.',
                'price' => 11.50,
                'unit' => 'jar (500g)',
                'stock_quantity' => 32,
                'weekly_quota' => 45,
                'status' => 'available',
                'is_featured' => false,
            ],
        ];

        $createdProducts = [];
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        foreach ($productsData as $prod) {
            $product = Product::firstOrCreate(['slug' => $prod['slug']], $prod);
            $createdProducts[] = $product;

            // Product Weekly Availability
            foreach ($days as $day) {
                $isDayAvail = in_array($day, ['Wednesday', 'Friday', 'Saturday', 'Sunday']);
                ProductAvailability::firstOrCreate(
                    ['product_id' => $product->id, 'day_of_week' => $day],
                    [
                        'available_quantity' => $isDayAvail ? (int) ($product->stock_quantity / 3) : 0,
                        'reserved_quantity' => 0,
                        'is_available' => $isDayAvail,
                    ]
                );
            }
        }

        // 8. Pickup Slots for Farmers
        $slotsData = [
            ['farmer_id' => $farmer1->id, 'market_id' => $market1->id, 'day_of_week' => 'Saturday', 'start_time' => '08:00:00', 'end_time' => '10:30:00', 'max_orders' => 20],
            ['farmer_id' => $farmer1->id, 'market_id' => $market1->id, 'day_of_week' => 'Saturday', 'start_time' => '10:30:00', 'end_time' => '13:00:00', 'max_orders' => 20],
            ['farmer_id' => $farmer1->id, 'market_id' => $market2->id, 'day_of_week' => 'Wednesday', 'start_time' => '09:00:00', 'end_time' => '12:00:00', 'max_orders' => 15],
            ['farmer_id' => $farmer2->id, 'market_id' => $market1->id, 'day_of_week' => 'Saturday', 'start_time' => '08:00:00', 'end_time' => '12:00:00', 'max_orders' => 25],
            ['farmer_id' => $farmer2->id, 'market_id' => $market3->id, 'day_of_week' => 'Friday', 'start_time' => '09:00:00', 'end_time' => '13:00:00', 'max_orders' => 15],
            ['farmer_id' => $farmer3->id, 'market_id' => $market2->id, 'day_of_week' => 'Saturday', 'start_time' => '09:00:00', 'end_time' => '12:30:00', 'max_orders' => 20],
            ['farmer_id' => $farmer3->id, 'market_id' => $market3->id, 'day_of_week' => 'Tuesday', 'start_time' => '08:30:00', 'end_time' => '11:30:00', 'max_orders' => 15],
        ];

        $createdSlots = [];
        foreach ($slotsData as $slot) {
            $createdSlots[] = PickupSlot::firstOrCreate(
                [
                    'farmer_id' => $slot['farmer_id'],
                    'market_id' => $slot['market_id'],
                    'day_of_week' => $slot['day_of_week'],
                    'start_time' => $slot['start_time'],
                ],
                $slot
            );
        }

        // 9. Sample Initial Orders with status progression
        $pickupDate = now()->next('Saturday')->format('Y-m-d');

        // Order 1: Completed order for Customer 1 with Farmer 1 (allows review!)
        $order1 = Order::firstOrCreate(
            ['order_number' => 'ML-ORD-20261001-1001'],
            [
                'customer_id' => $customer1->id,
                'farmer_id' => $farmer1->id,
                'market_id' => $market1->id,
                'pickup_slot_id' => $createdSlots[0]->id,
                'pickup_date' => now()->subDays(5)->format('Y-m-d'),
                'pickup_time' => '08:00 AM - 10:30 AM',
                'total_amount' => 12.10,
                'status' => Order::STATUS_COMPLETED,
                'payment_method' => 'cash_on_pickup',
                'payment_status' => 'paid',
                'customer_name' => 'John Doe',
                'customer_phone' => '+1 (555) 987-6543',
                'notes' => 'Please bundle the carrots cleanly.',
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $order1->id, 'product_id' => $createdProducts[0]->id],
            [
                'product_name' => $createdProducts[0]->name,
                'unit' => $createdProducts[0]->unit,
                'unit_price' => $createdProducts[0]->price,
                'quantity' => 2,
                'subtotal' => 7.60,
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $order1->id, 'product_id' => $createdProducts[1]->id],
            [
                'product_name' => $createdProducts[1]->name,
                'unit' => $createdProducts[1]->unit,
                'unit_price' => $createdProducts[1]->price,
                'quantity' => 1,
                'subtotal' => 4.50,
            ]
        );

        // Order 2: Pending order for Customer 1 with Farmer 2
        $order2 = Order::firstOrCreate(
            ['order_number' => 'ML-ORD-20261017-2002'],
            [
                'customer_id' => $customer1->id,
                'farmer_id' => $farmer2->id,
                'market_id' => $market1->id,
                'pickup_slot_id' => $createdSlots[3]->id,
                'pickup_date' => $pickupDate,
                'pickup_time' => '08:00 AM - 12:00 PM',
                'total_amount' => 10.40,
                'status' => Order::STATUS_PENDING,
                'payment_method' => 'cash_on_pickup',
                'payment_status' => 'unpaid',
                'customer_name' => 'John Doe',
                'customer_phone' => '+1 (555) 987-6543',
                'notes' => 'Looking forward to the Honeycrisp apples!',
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $order2->id, 'product_id' => $createdProducts[3]->id],
            [
                'product_name' => $createdProducts[3]->name,
                'unit' => $createdProducts[3]->unit,
                'unit_price' => $createdProducts[3]->price,
                'quantity' => 1,
                'subtotal' => 4.90,
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $order2->id, 'product_id' => $createdProducts[4]->id],
            [
                'product_name' => $createdProducts[4]->name,
                'unit' => $createdProducts[4]->unit,
                'unit_price' => $createdProducts[4]->price,
                'quantity' => 1,
                'subtotal' => 5.50,
            ]
        );

        // 10. Verified Reviews
        Review::firstOrCreate(
            ['order_id' => $order1->id, 'product_id' => $createdProducts[0]->id],
            [
                'customer_id' => $customer1->id,
                'farmer_id' => $farmer1->id,
                'rating' => 5,
                'comment' => 'The rainbow carrots were sweet, vibrant, and so fresh. My kids loved them roasted with a bit of butter. Excellent quality!',
                'farmer_reply' => 'Thank you so much John! The rainbow variety is our favorite too. Hope to see you next Saturday.',
                'replied_at' => now()->subDays(2),
                'is_moderated' => true,
            ]
        );

        Review::firstOrCreate(
            ['order_id' => $order1->id, 'product_id' => $createdProducts[1]->id],
            [
                'customer_id' => $customer1->id,
                'farmer_id' => $farmer1->id,
                'rating' => 5,
                'comment' => 'True heirloom flavor. The Brandywine tomatoes were firm and fragrant. Best tomatoes in Springfield!',
                'is_moderated' => true,
            ]
        );

        // 11. Customer Favorites
        Favorite::firstOrCreate([
            'customer_id' => $customer1->id,
            'product_id' => $createdProducts[0]->id,
        ]);
        Favorite::firstOrCreate([
            'customer_id' => $customer1->id,
            'farmer_id' => $farmer1->id,
        ]);

        // 12. Active Cart for Customer 1
        Cart::firstOrCreate(
            ['customer_id' => $customer1->id, 'product_id' => $createdProducts[7]->id],
            ['quantity' => 2]
        );

        // 13. Customer - Farmer Conversation and Messages
        $convo = Conversation::firstOrCreate(
            ['customer_id' => $customer1->id, 'farmer_id' => $farmer1->id],
            [
                'product_id' => $createdProducts[0]->id,
                'order_id' => $order1->id,
                'last_message_at' => now()->subHours(2),
            ]
        );

        Message::firstOrCreate(
            ['conversation_id' => $convo->id, 'sender_id' => $customer1->id, 'message' => 'Hello Thomas, will you have purple carrots again this weekend?'],
            ['is_read' => true, 'created_at' => now()->subHours(4)]
        );

        Message::firstOrCreate(
            ['conversation_id' => $convo->id, 'sender_id' => $farmer1->id, 'message' => 'Hi John! Yes, we have a fresh bed ready for Saturday harvest. You can pre-order right now!'],
            ['is_read' => true, 'created_at' => now()->subHours(2)]
        );

        // 14. Notifications
        Notification::send(
            $farmer1->id,
            'New Pre-Order Received',
            'Order #ML-ORD-20261001-1001 was completed successfully.',
            'order',
            '/farmer/orders/' . $order1->id
        );

        Notification::send(
            $customer1->id,
            'Order Confirmed',
            'Your pre-order with Green Pastures Organic Farm is pending review.',
            'order',
            '/customer/orders/' . $order2->id
        );

        // 15. Platform Announcements
        Announcement::firstOrCreate(
            ['title' => 'Spring Harvest Season Opening!'],
            [
                'admin_id' => $admin->id,
                'message' => 'Welcome to the new CropCircle season! All market stalls at Springfield Downtown Plaza and Valley Green are now accepting online pre-orders.',
                'target_role' => 'all',
                'is_active' => true,
                'expires_at' => now()->addDays(30),
            ]
        );

        Announcement::firstOrCreate(
            ['title' => 'Farmer Pickup Schedule Guidelines'],
            [
                'admin_id' => $admin->id,
                'message' => 'Please ensure your weekly product quotas and pickup windows are updated by Thursday midnight to allow smooth customer reservations.',
                'target_role' => 'farmer',
                'is_active' => true,
                'expires_at' => now()->addDays(60),
            ]
        );

        // 16. Audit Activity Log
        ActivityLog::record($admin->id, 'platform_initialized', 'CropCircle platform core database seeded and configured.');
    }
}
