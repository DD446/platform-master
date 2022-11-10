<?php
/**
 * User: fabio
 * Date: 04.01.22
 * Time: 17:35
 */

namespace App\Classes;

use Illuminate\Support\Facades\Log;

class ProcessWebhookJob extends \Spatie\WebhookClient\ProcessWebhookJob
{
    public function handle()
    {
        Log::debug("ProcessWebhookJob::handle");
    }
}
