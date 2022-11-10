<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateSpotifyMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var array
     */
    private $feeds;

    /**
     * @var array
     */
    private $custom;

    /**
     * @var string
     */
    private $newest;

    /**
     * @var string
     */
    private $latest;

    /**
     * @var array
     */
    private $feedsWithTime;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $feeds, array $custom, array $latest)
    {
        $this->feeds = $feeds;
        $this->custom = $custom;
        $this->feedsWithTime = $latest;
        $this->newest = config('app.name') . '_new_submissions_spotify_' . today()->toDateString() . '.csv';
        $this->latest = config('app.name') . '_latest_250_submissions_spotify_' . today()->toDateString() . '.csv';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('spotify.emails.create', ['custom' => implode(PHP_EOL, $this->custom)])
            ->subject(trans('spotify.mail_subject_new_submissions'))
            ->attachData($this->getCsv($this->feeds, ['RSS-Feed']), $this->newest, ['mime' => 'text/csv'])
            ->attachData($this->getCsv($this->feedsWithTime, ['RSS-Feed', 'Veröffentlichungsdatum']), $this->latest, ['mime' => 'text/csv'])
            ->from('fabio@podcaster.de');
    }

    private function getCsv(array $source, array $header)
    {
        $content = '';
        $handle = tmpfile();
        fputcsv($handle, $header);

        foreach($source as $a) {
            fputcsv($handle,  (array)$a);
        }

        rewind($handle);
        while (!feof($handle)) {
            $content .= fread($handle, 8192);
        }
        fclose($handle);

        return $content;
    }
}
