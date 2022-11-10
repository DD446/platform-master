<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDpaAttribute extends Model
{
    const TYPE_DATA = 1;
    const TYPE_CONCERNED = 2;

    public $timestamps = false;

    static $aData = [
        'meta' => 'meta',
        'contact' => 'contact',
        'content' => 'content',
    ];

    static $aConcerned =[
        'users' => 'Nutzer des Auftraggebers',
    ];

    protected $fillable = [
        'data',
        'type',
    ];

    /**
     * UserDpaAttribute constructor.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        self::$aData = [
            'meta' => trans('privacy.user_dpa_attribute_meta'),
            'contact' => trans('privacy.user_dpa_attribute_contact'),
            'content' => trans('privacy.user_dpa_attribute_content'),
        ];

        self::$aConcerned = [
            'users' => 'Nutzer des Auftraggebers',
        ];
    }


    public static function spoken($data, $type = self::TYPE_DATA)
    {
        if ($type == self::TYPE_DATA) {
            if (array_key_exists($data, self::$aData)) {
                return self::$aData[$data];
            }
        } elseif($type == self::TYPE_CONCERNED) {
            if (array_key_exists($data, self::$aConcerned)) {
                return self::$aConcerned[$data];
            }
        }

        return null;
    }

}
