<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Itunes implements Castable
{
    public static function castUsing(array $arguments)
    {
        return new class implements CastsAttributes
        {
            public function get($model, $key, $value, $attributes)
            {
                $attributes  = $attributes['itunes'] ?? [];

                return [
                    'title' => $attributes['title'] ?? null,
                    'subtitle' => $attributes['subtitle'] ?? null,
                    'summary' => $attributes['summary'] ?? null,
                    'description' => $attributes['description'] ?? null,
                    'episode' => $attributes['episode'] ?? null,
                    'episodeType' => $attributes['episodeType'] ?? null,
                    'season' => $attributes['season'] ?? null,
                    'logo' => $attributes['logo'] ?? null,
                    'duration' => $this->getCleanDuration($attributes['duration'] ?? null),
                    'explicit' => $this->getCleanExplicit($attributes['explicit'] ?? false),
                    'isclosedcaptioned' => $this->getCleanIsclosedcaptioned($attributes['isclosedcaptioned'] ?? false),
                    'author' => $attributes['author'] ?? null,
                ];
            }

            public function set($model, $key, $value, $attributes)
            {
                return [
                    'title' => $value->title ?? '',
                    'subtitle' => Str::limit($value->subtitle ?? '', 255),
                    'summary' => Str::limit($value->summary ?? '', 400),
                    'description' => Str::limit($value->description ?? '', 4000),
                    'episode' => isset($value->episode) ? (int) $value->episode : null,
                    'episodeType' => $value->episodeType ?? null,
                    'season' => isset($value->season) ? (int) $value->season : null,
                    'logo' => isset($value->logo) ? (int) $value->logo : null,
                    'duration' => $this->getCleanDuration($value->duration ?? null),
                    'explicit' => $this->getCleanExplicit($value->explicit ?? null),
                    'isclosedcaptioned' => $this->getCleanIsclosedcaptioned($value->isclosedcaptioned ?? null),
                    'author' => Str::limit($value->author ?? '', 255),
                ];
            }

            private function getCleanDuration($value)
            {
                if (!$value) {
                    return '00:00';
                }
                if (Str::startsWith($value, '00:')) {
                    return Str::after($value, '00:');
                }
                return $value;
            }

            private function getCleanExplicit($value)
            {
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }

            private function getCleanIsclosedcaptioned($value)
            {
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }
        };
    }
}
