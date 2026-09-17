<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        $userId = User::query()->inRandomOrder()->value('id');

        return [
            'user_id' => $userId,
            'company_name' => fake()->company(),
            'contact_person' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'mobile' => fake()->unique()->numerify('9#########'),
            'alternate_mobile' => fake()->optional()->numerify('9#########'),
            'address1' => fake()->streetAddress(),
            'address2' => fake()->optional()->secondaryAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'country' => 'India',
            'pincode' => fake()->numerify('######'),
            'website' => fake()->optional()->url(),
            'gst_number' => fake()->optional()->bothify('??##########??'),
            'pan_number' => fake()->optional()->bothify('?????####?'),
            'notes' => fake()->optional()->sentence(),
            'status' => true,
            'created_by' => $userId,
            'updated_by' => $userId,
        ];
    }
}
