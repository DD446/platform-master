@component('mail::message')
    @component('mail::panel')
        @lang('spotify.mail_panel_new_submissions')
    @endcomponent

    @lang('spotify.mail_text_custom_submissions')
    <br>
    @lang('spotify.mail_text_list_custom_submissions', ['urls' => $custom])
    <br>
    @lang('spotify.mail_text_new_submissions', ['date' => today()->toDateString()])
    <br>
    @component('mail::subcopy')
        @lang('spotify.mail_goodbye_informal')
    @endcomponent
@endcomponent