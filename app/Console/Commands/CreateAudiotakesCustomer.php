<?php

namespace App\Console\Commands;

use App\Models\AudiotakesContract;
use App\Models\Feed;
use App\Models\User;
use Illuminate\Console\Command;

class CreateAudiotakesCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audiotakes:create-customer {userId : The ID of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates an audiotakes contracts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $userId = $this->argument('userId');

        if (strpos($userId, '@') !== false) {
            $user = User::where('email', '=', $userId)->firstOrFail();
        } elseif(is_numeric($userId)) {
            $user = User::findOrFail($userId);
        } else {
            $user = User::where('username', '=', $userId)->firstOrFail();
        }

        $username = $user->username;
        $username .= " <" . $user->email . ">";
        $username .= " (" . $user->first_name . ' ' . $user->last_name . ")";

        $feeds = Feed::where('username', '=', $user->username)
            ->pluck('feed_id')
            ->toArray();

        $feedId = $this->choice('Welcher Feed soll vermarktet werden?', $feeds, 0);

        if ($this->confirm('Soll der Feed "' . $feedId . '" für den Benutzer "' . $username . '" vermarktet werden?')) {

            $identifier = null;

            if ($this->confirm("Gibt es schon einen Identifier für den Feed {$feedId}?")) {
                $identifier = $this->ask('Wie lautet der Identifier (fängt mit at- oder pp- an)?');
                $ai = AudiotakesContract::whereIdentifier($identifier)->pluck('feed_id')->first();

                if ($ai && $ai != $feedId) {
                    $this->error("Der Identifier {$identifier} wird bereits für den Feed {$ai} verwendet.");
                }
            }

            if (!$identifier) {
                $identifier = AudiotakesContract::getFreeIdentifier();
            }

            $ac = AudiotakesContract::whereIdentifier($identifier)->firstOrFail();
            $ac->user_id = $user->user_id;
            $ac->first_name = $this->anticipate("Vorname?", [$user->first_name], $user->first_name);
            $ac->last_name = $this->anticipate("Nachname?", [$user->last_name], $user->last_name);
            $ac->email = $this->anticipate("E-Mail?", [$user->email], $user->email);
            $ac->street = $this->anticipate("Straße?", [$user->street], $user->street);
            $ac->housenumber = $this->anticipate("Hausnummer?", [$user->housenumber], $user->housenumber);
            $ac->post_code = $this->anticipate("Postleitzahl?", [$user->post_code], $user->post_code);
            $ac->city = $this->anticipate("Ort?", [$user->city], $user->city);
            $ac->country = $this->anticipate("Land?", [$user->country], $user->country);
            $ac->organisation = $this->anticipate("Organisation?", [$user->organisation]);
            $ac->vat_id = $this->anticipate("Umsatzsteuer-ID?", [$user->vat_id]);

            if ($ac->update(['feed_id' => $feedId, 'user_id' => $user->user_id])) {
                $feed = Feed::whereUsername($user->username)->whereFeedId($feedId)->firstOrFail();
                $settings = $feed->settings;
                $settings['audiotakes_id'] = $identifier;

                if ($feed->whereUsername($user->username)
                    ->whereFeedId($feedId)
                    ->update(['settings' => $settings])) {

                    if ($this->confirm("Möchtest Du die Vermarktung für den Feed {$feedId} jetzt aktivieren?")) {
                        $settings['audiotakes'] = 1;
                        if ($feed->whereUsername($user->username)
                            ->whereFeedId($feedId)
                            ->update(['settings' => $settings])) {
                            refresh_feed($user->username, $feedId);
                            $this->line("Der Feed {$feedId} ist jetzt in der Vermarktung.");
                        }
                    }
                }
            }
        }

        return 0;
    }
}
