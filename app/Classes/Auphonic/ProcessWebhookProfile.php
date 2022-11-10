<?php
/**
 * User: fabio
 * Date: 04.01.22
 * Time: 17:45
 */

namespace App\Classes\Auphonic;

use App\Models\WebhookSend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProcessWebhookProfile implements \Spatie\WebhookClient\WebhookProfile\WebhookProfile
{

    public function shouldProcess(Request $request): bool
    {
        Log::debug("ProcessWebhookProfile::shouldProcess");

        $uuid = $request->get('uuid');

        return WebhookSend::where('service', '=', 'auphonic')->where('payload->uuid', '=', $uuid)->count() == 1;
    }
}
