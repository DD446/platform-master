<?php

namespace App\Http\Controllers;

use App\Models\UserOauth;
use Illuminate\Http\Request;

class FacebookController extends Controller
{
    public function oauth()
    {
        $server = new \League\OAuth1\Client\Server\Facebook([
            'identifier' => config('services.facebook.client_id'),
            'secret' => config('services.facebook.client_secret'),
            'callback_uri' => route('facebook.callback'),
        ]);

        // Retrieve temporary credentials
        $temporaryCredentials = $server->getTemporaryCredentials();

        // Store credentials in the session, we'll need them later
        session(['temporary_credentials' => serialize($temporaryCredentials)]);

        // Second part of OAuth 1.0 authentication is to redirect the
        // resource owner to the login screen on the server.
        $server->authorize($temporaryCredentials);
    }

    public function callback()
    {
        $server = new \League\OAuth1\Client\Server\Facebook([
            'identifier' => config('services.facebook.client_id'),
            'secret' => config('services.facebook.client_secret'),
            'callback_uri' => route('facebook.callback'),
        ]);

        if (request()->has('oauth_token') && request()->has('oauth_verifier')) {
            // Retrieve the temporary credentials we saved before
            $temporaryCredentials = unserialize(session('temporary_credentials'));

            // We will now retrieve token credentials from the server
            $tokenCredentials = $server->getTokenCredentials($temporaryCredentials, request('oauth_token'), request('oauth_verifier'));

            // User is an instance of League\OAuth1\Client\Server\User
            //$user = $server->getUserDetails($tokenCredentials);

            // UID is a string / integer unique representation of the user
            //$uid = $server->getUserUid($tokenCredentials);

            // Email is either a string or null (as some providers do not supply this data)
            //$email = $server->getUserEmail($tokenCredentials);

            // Screen name is also known as a username (Facebook handle etc)
            $screenName = $server->getUserScreenName($tokenCredentials);

            $uo = new UserOauth();
            $uo->user_id = auth()->user()->user_id;
            $uo->screen_name = $screenName;
            $uo->oauth_token = serialize($tokenCredentials);
            $uo->service = 'facebook';

            if ($uo->save()) {
                // TODO: Redirect with success message
            }
        }

        return response()->redirectTo('/freigaben');
    }
}
