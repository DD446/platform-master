<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Faq
 * 
 * @property int $faq_id
 * @property Carbon|null $date_created
 * @property Carbon|null $last_updated
 * @property string|null $question
 * @property string|null $answer
 * @property int|null $item_order
 * @property int|null $is_hidden
 * @property int|null $category_id
 * @property int $likes
 * @property int $dislikes
 *
 * @package App\Models\Base
 */
class Faq extends Model
{
	protected $table = 'faq';
	protected $primaryKey = 'faq_id';
	public $timestamps = false;

	protected $casts = [
		'item_order' => 'int',
		'is_hidden' => 'int',
		'category_id' => 'int',
		'likes' => 'int',
		'dislikes' => 'int'
	];

	protected $dates = [
		'date_created',
		'last_updated'
	];
}
