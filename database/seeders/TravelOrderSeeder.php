<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TravelOrder;
use App\Models\User;
use Faker\Factory as FakerFactory;

class TravelOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $faker = FakerFactory::create(config('app.faker_locale'));
        
        if (User::count() === 0) {
            User::factory(10)->create();
        }

        $userIds = User::pluck('id')->all();
        foreach (range(1, 20) as $index) {
            TravelOrder::factory()->create([
                'user_id' => $faker->randomElement($userIds),
            ]);
        }
    }
}