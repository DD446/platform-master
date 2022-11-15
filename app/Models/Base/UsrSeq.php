<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UsrSeq
 * 
 * @property int $id
 *
 * @package App\Models\Base
 */
class UsrSeq extends Model
{
	protected $table = 'usr_seq';
	public $timestamps = false;
}
