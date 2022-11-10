<?php
namespace Database\Factories;

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Feed;
use App\Models\Show;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Feed::class;

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (Feed $feed) {

        })->afterCreating(function (Feed $feed) {
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $username = User::all()->random()->username;
        $feedId = Str::replaceArray('.', [''], $this->faker->text(16));

        return [
            'feed_id' => $feedId,
            'username' => $username,
            'domain' => [
                'tld' => 'de',
                'subdomain' => $username . '.podcaster',
                'domain' => 'podcaster.de',
                'hostname' => 'https://' . $username . '.podcaster.de',
                'protocol' => 'https://',
                'is_custom' => false,
                'website_type' => false,
                'website_redirect' => false,
                'feed_redirect' => false,
            ],
            'settings' => [
                'feed_entries' => $this->faker->numberBetween(1, 5000),
                'ping' => $this->faker->numberBetween(0, 1),
                'default_item_title' => null,
                'default_item_description' => null,
                'chartable' => $this->faker->numberBetween(0, 1),
                'chartable_id' => $this->faker->text(6),
                'rms' => $this->faker->numberBetween(0, 1),
                'podcorn' => $this->faker->numberBetween(0, 1),
                'spotify' => $this->faker->numberBetween(0, 1),
                'spotify_uri' => null,
            ],
            'rss' => [
                'title' => $this->faker->text(64),
                'description' => $this->faker->text(4000),
                'copyright' => $this->faker->text(255),
            ],
            'itunes' => [
                'subtitle' => $this->faker->text(32)
            ],
            'googleplay' => [
                'author' => $this->faker->name(),
                'description' => $this->faker->text(),
            ],
            'entries' => [
                [
                    'title' => $this->faker->text(64),
                    'guid' => get_guid('pod'),
                    'is_public' => Show::PUBLISH_NOW,
                    'show_media' => time(),
                ]
            ],
        ];
    }
}
