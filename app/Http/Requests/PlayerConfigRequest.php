<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\PlayerConfig;

class PlayerConfigRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->method() == 'PUT') {
            $config = PlayerConfig::find($this->route('config'));
            return $this->user()->can('update', $config);
        }
        if ($this->method() == 'POST') {
            return $this->user()->can('create', 'App\Models\PlayerConfig');
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'player_type' => 'required|numeric',
            'title' => 'required|string',
            'delay_between_audio' => 'required|numeric',
            'initial_playback_speed' => 'required|numeric',
            'hide_playlist_in_singlemode' => 'required|bool',
            'show_playlist' => 'required|bool',
            'show_info' => 'required|bool',
            'player_configurable_type' => 'required|string|in:' . PlayerConfig::TYPE_SHOW . ',' . PlayerConfig::TYPE_CHANNEL . ',' . PlayerConfig::TYPE_DIRECT,
            'player_configurable_id' => 'required|string',
            'feed_id' => 'required_unless:player_configurable_type,' . PlayerConfig::TYPE_DIRECT . '|string',
            'sharing' => 'bool',
            'background_color' => 'string|nullable',
            'text_color' => 'string|nullable',
            'icon_color' => 'string|nullable',
            'icon_fg_color' => 'string|nullable',
            'progressbar_color' => 'string|nullable',
            'progressbar_buffer_color' => 'string|nullable',
            'custom_styles' => 'string|nullable',
            'preload' => 'in:none,metadata,audio',
            'order' => 'in:date_desc,date_asc,itunes_desc,itunes_asc',
        ];
    }

    public function attributes()
    {
        return [
            'title' => trans('player.text_configurator_title'),
        ];
    }


}
