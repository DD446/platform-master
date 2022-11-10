@component('mail::message')
    {{ trans_choice('feedback.type', $feedback->type) }}

    {{ $feedback->comment }}

    {{ $feedback->entity }}

    --
    {{ $feedback->user->name }} <{{ $feedback->user->email }}>

    {{ $feedback->user->username }} ({{ $feedback->user->usr_id }})
@endcomponent
