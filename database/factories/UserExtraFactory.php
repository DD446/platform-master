<?php
namespace Database\Factories;

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserExtraFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\UserExtra::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'usr_id' => $this->faker->numberBetween(1, User::count()),
            'extras_type' => $this->faker->numberBetween(1, 4),
            'extras_count' => $this->faker->numberBetween(1, 100),
            'extras_description' => $this->faker->text(25),
            'date_created' => $this->faker->dateTime(),
            'date_start' => $this->faker->dateTime(),
            'date_end' => $this->faker->dateTime('30 days'),
            'is_repeating' => $this->faker->numberBetween(0, 1),
        ];
    }
}
