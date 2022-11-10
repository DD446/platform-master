<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Campaign;
use App\Models\Feed;

class CampaignInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;
    /**
     * @var Campaign
     */
    private $campaign;
    /**
     * @var Feed
     */
    private $feed;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Campaign $campaign, Feed $feed)
    {
        //
        $this->campaign = $campaign;
        $this->feed = $feed;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->markdown('campaigns.invitations.notification', ['campaign' => $this->campaign, 'feed' => $this->feed])
            ->subject('Einladung zur Teilnahme an einer Kampagne')
            ->replyTo($this->campaign->reply_to);

/*        return (new MailMessage)
            ->greeting('Hallo Podcaster!')
            ->subject('Einladung zur Teilnahme an einer Kampagne')
            ->line('Es liegt eine neue Anfrage für Werbung in Deinem Podcast "' . $notifiable->feed->rss['title'] . '" vor.')
            ->line($notifiable->campaign->description)
            ->line('Um weitere ')
            ->line('Du erhältst diese Mail, weil Du Interesse an Werbung in Deinem Podcast signalisiert hast. Du kannst den Empfang dieser Angebote in den <a href="/podcast/' . $notifiable->feed->feed_id . '/">Einstellungen zu Deinem Podcast</a> abschalten.')
            ->salutation('')*/
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
