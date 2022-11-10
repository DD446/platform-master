<?php
/**
 * User: fabio
 * Date: 22.05.22
 * Time: 22:19
 */

namespace App\Classes\Auphonic;

use App\Models\WebhookSend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\WebhookClient\WebhookConfig;

class SignatureValidator implements \Spatie\WebhookClient\SignatureValidator\SignatureValidator
{

    public function isValid(Request $request, WebhookConfig $config): bool
    {
        Log::debug('SignatureValidator::isValid');

        return $request->has('uuid') &&
            $request->has('status') &&
            $request->has('status_string');
    }
}
