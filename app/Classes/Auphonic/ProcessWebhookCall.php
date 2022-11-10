<?php
/**
 * User: fabio
 * Date: 04.01.22
 * Time: 18:16
 */

namespace App\Classes\Auphonic;

class ProcessWebhookCall extends \Spatie\WebhookClient\Models\WebhookCall
{
    protected $table = 'webhook_calls';
}
