<?php

/**
 */

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserPaymentsSeq
 *
 * @property int $id
 *
 * @package App\Models\Base
 */
class UserPaymentsSeq extends Model
{
	protected $table = 'user_payments_seq';

	public $timestamps = false;
}
