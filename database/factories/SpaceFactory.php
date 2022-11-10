<?php
namespace Database\Factories;

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpaceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Space::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => $this->faker->numberBetween(1, User::count()),
            'user_accounting_id' => $this->faker->randomNumber(),
            'space' => $this->faker->randomNumber(),
            'space_available' => $this->faker->randomNumber(),
            'type' => $this->faker->numberBetween(1, 2),
            'is_available' => $this->faker->boolean(),
            'is_free' => $this->faker->boolean(),
        ];
    }
}
