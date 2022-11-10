<?php

namespace App\Mail;

use App\Models\Feed;
use App\Models\PodcastRouletteMatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PodcastRouletteMatchMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private PodcastRouletteMatch $match;

    /**
     * @var false
     */
    private bool $usePartner;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PodcastRouletteMatch $match, $usePartner = false)
    {
        $this->match = $match;
        $this->usePartner = $usePartner;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->usePartner) {
            $user = $this->match->partner;
        } else {
            $user = $this->match->player;
        }
        $partner = $user->podcasters;
        $feed = Feed::whereUsername($user->user->username)->whereFeedId($user->feed_id)->select(['rss.title'])->first();
        $podcastTitle = $feed->rss['title'];
        $email = $user->email;

        return $this
            ->from('steffen+podcastroulette@podcaster.de', 'Steffen Wrede')
            ->markdown('emails.podcast-roulette-match-mail', compact('partner', 'podcastTitle', 'email'));
    }
}
