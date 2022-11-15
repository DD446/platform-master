<?php

namespace App\Models;

use App\Models\Base\WebsocketsStatisticsEntry as BaseWebsocketsStatisticsEntry;

class WebsocketsStatisticsEntry extends BaseWebsocketsStatisticsEntry
{
	protected $fillable = [
		'app_id',
		'peak_connection_count',
		'websocket_message_count',
		'api_message_count'
	];
}
