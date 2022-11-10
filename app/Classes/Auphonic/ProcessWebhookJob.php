<?php
/**
 * User: fabio
 * Date: 04.01.22
 * Time: 17:35
 */

namespace App\Classes\Auphonic;

use App\Classes\AuphonicManager;
use App\Events\AuphonicProductionEvent;
use App\Models\WebhookSend;
use Illuminate\Support\Facades\Log;

class ProcessWebhookJob extends \Spatie\WebhookClient\ProcessWebhookJob
{
    public function handle()
    {
        Log::debug("ProcessWebhookJob::handle");

        //$name = $this->webhookCall->name;
        $payload = $this->webhookCall->payload;
        $uuid = $payload['uuid'];
        $status = $payload['status'];

        // perform the work here
        WebhookSend::where('service', '=', 'auphonic')->where('payload->uuid', '=', $uuid)->update([
            'status' => $status,
        ]);

        event(new AuphonicProductionEvent($this->webhookCall));
    }
}
