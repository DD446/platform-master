<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Language
 * 
 * @property string $id
 * @property string $value
 *
 * @package App\Models\Base
 */
class Language extends Model
{
	protected $table = 'languages';
	public $incrementing = false;
	public $timestamps = false;
}
