<?php
namespace Database\Factories;

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\PlayerConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlayerConfigFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PlayerConfig::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => $this->faker->numberBetween(1, User::count()),
            'uuid' => Str::uuid(),
            'player_type' => $this->faker->numberBetween(1, 3),
            'title' => $this->faker->text(15),
            'default_album_art' => $this->faker->imageUrl(),
            'delay_between_audio' => $this->faker->randomFloat(2, 0, 3),
            'initial_playback_speed' => $this->faker->numberBetween(1, 2),
            'hide_playlist_in_singlemode' => $this->faker->boolean,
            'show_playlist' => $this->faker->boolean,
            'show_info' => $this->faker->boolean,
            'enable_shuffle' => $this->faker->boolean,
            'debug_player' => $this->faker->boolean,
            'player_configurable_id' => $this->faker->text(15),
            'preload' => Arr::random(['none', 'metadata', 'audio']),
            'player_configurable_type' => Arr::random(['channel', 'show']),
            'feed_id' => $this->faker->text(15),
            'sharing' => $this->faker->boolean,
            'text_color' => $this->faker->hexColor,
            'background_color' => $this->faker->hexColor,
            'icon_color' => $this->faker->hexColor,
            'icon_fg_color' => $this->faker->hexColor,
            'progressbar_color' => $this->faker->hexColor,
            'progressbar_buffer_color' => $this->faker->hexColor,
            'custom_styles' => '',
            'order' => Arr::random(['date_desc', 'date_asc', 'itunes_asc', 'itunes_desc']),
        ];
    }
}
