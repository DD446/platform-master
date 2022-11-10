@component('mail::message')
@component('mail::panel')
@lang('teams.header_notification_invitation')
@endcomponent

# @lang('teams.text_notification_invitation_welcome')

@lang('teams.text_notification_invitation', ['name' => $data->team->user->fullname, 'email' => $data->team->user->email])

@component('mail::button', ['url' => route('invite.show', ['invite' => $data->hash, 'id' => $data->id])])
@lang('teams.text_notification_invitation_button_accept')
@endcomponent

@lang('teams.text_notification_invitation_disclaimer', ['until' => $data->created_at, 'to' => $data->email])

@component('mail::subcopy')
@lang('teams.text_notification_invitation_footer', ['service' => config('app.name')])
@endcomponent
@endcomponent
