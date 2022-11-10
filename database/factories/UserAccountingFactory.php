<?php
namespace Database\Factories;

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use App\Models\UserAccounting;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserAccountingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\UserAccounting::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'usr_id' => $this->faker->numberBetween(1, User::count()),
            'activity_type' => $this->faker->numberBetween(1, 5),
            'activity_characteristic' => $this->faker->numberBetween(1, 5),
            'activity_description' => $this->faker->text(155),
            'amount' => $this->faker->randomFloat(2, -100, 100),
            'currency' => $this->faker->currencyCode,
            'date_created' => $this->faker->dateTime(),
            'date_start' => $this->faker->dateTime(),
            'date_end' => $this->faker->dateTime('30 days'),
            'procedure' => $this->faker->numberBetween(1, 2),
            'status' => $this->faker->numberBetween(0, 1),
        ];
    }
}
