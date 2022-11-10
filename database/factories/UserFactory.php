<?php
namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        static $password;
        $username = Str::replaceArray('.', [''], $this->faker->userName);

        return [
            'username' => $username,
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'passwd' => $password ?: $password = md5('secret'),
            'password' => $password ?: $password = bcrypt('secret'),
            'remember_token' => Str::random(10),
            'telephone' => Str::limit($this->faker->phoneNumber, 16, ''),
            'telefax' => Str::limit($this->faker->phoneNumber, 16, ''),
            'url' => $this->faker->url,
            'organisation' => $this->faker->company,
            'street' => $this->faker->streetName,
            'housenumber' => $this->faker->streetAddress,
            'feed_email' => $this->faker->companyEmail,
            'city' => $this->faker->city,
            //'region' => $this->faker->,
            'country' => $this->faker->countryCode,
            'post_code' => $this->faker->postcode,
            'gender' => Arr::random(['m', 'w']),
            'is_acct_active' => $this->faker->numberBetween(0, 1),
            'is_trial' => $this->faker->numberBetween(-2, 1),
            'package_id' => $this->faker->numberBetween(0, 7),
            'is_supporter' => $this->faker->numberBetween(0, 1),
            'is_advertiser' => $this->faker->numberBetween(0, 1),
            'can_pay_by_bill' => $this->faker->numberBetween(0, 1),
            'funds' => $this->faker->randomFloat(2, 0, 500),
            'role_id' => $this->faker->numberBetween(0, 3),
        ];
    }
}
