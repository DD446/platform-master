<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NovaTrixAttachment
 * 
 * @property int $id
 * @property string $attachable_type
 * @property int $attachable_id
 * @property string $attachment
 * @property string $disk
 * @property string $url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class NovaTrixAttachment extends Model
{
	protected $table = 'nova_trix_attachments';

	protected $casts = [
		'attachable_id' => 'int'
	];
}
