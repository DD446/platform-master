<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NovaPendingTrixAttachment
 * 
 * @property int $id
 * @property string $draft_id
 * @property string $attachment
 * @property string $disk
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class NovaPendingTrixAttachment extends Model
{
	protected $table = 'nova_pending_trix_attachments';
}
