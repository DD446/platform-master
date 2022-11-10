@component('mail::message')
    {{ trans('contact_us.enquiry.' . $contactUs->enquiry_type) }}

    {{ $contactUs->comment }}

    ---
    {{ $contactUs->name }} <{{ $contactUs->email }}>

    {{ $contactUs->user->username }} ({{ $contactUs->user->usr_id }})
@endcomponent