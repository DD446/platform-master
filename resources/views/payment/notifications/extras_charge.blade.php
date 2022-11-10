@component('mail::message')
@component('mail::panel')
@lang('main.text_order_extra_notification_header')
@endcomponent

# @lang('main.text_order_extra_notification_welcome', ['name' => $data->user->first_name])

@lang('main.text_order_extra_notification_intro')

@lang('main.text_order_extra_notification_detail', ['order' => $data->activity_description, 'amount' => $data->amount, 'currency' => $data->currency, 'funds' => $data->user->funds])

@if($data->user->funds < 0)
@lang('main.text_order_extra_notification_hint_funds')
@endif

@component('mail::button', ['url' => route('accounting.index')])
@lang('main.text_order_extra_notification_button_funds')
@endcomponent

@lang('main.text_order_extra_notification_hint_extras')

@component('mail::button', ['url' => route('extras.index'), 'color' => 'success'])
@lang('main.text_order_extra_notification_button_extras')
@endcomponent

@component('mail::subcopy')
@lang('main.text_order_extra_notification_footer', ['service' => config('app.name')])
@endcomponent
@endcomponent
