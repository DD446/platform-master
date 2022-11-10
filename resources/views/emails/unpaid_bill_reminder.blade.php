@component('mail::message')
# {{ $salutation }}

{{ $intro }}

@lang('bills.mail_hint_bill_data_unpaid_bill_reminder')

@component('mail::panel')
<p style="text-align:center">
@lang('bills.mail_header_bill_data_unpaid_bill_reminder')
</p>
@component('mail::table')
|||
| ------------- | -------------: |
| @lang('bills.mail_table_receiver_unpaid_bill_reminder') | @lang('bills.billing_company_name') |
| @lang('bills.mail_table_bank_unpaid_bill_reminder') | @lang('bills.billing_company_bank_name') |
| @lang('bills.mail_table_bic_swift_unpaid_bill_reminder') | @lang('bills.billing_company_bank_bic') |
| @lang('bills.mail_table_iban_unpaid_bill_reminder') | @lang('bills.billing_company_bank_iban') |
| @lang('bills.mail_table_purpose_unpaid_bill_reminder') | {{ $payment->bill_id }} |
| **@lang('bills.mail_table_due_date_unpaid_bill_reminder')** | {{ $payment->date_created->addDays(10)->formatLocalized('%d.%m.%Y') }} |
| **@lang('bills.mail_table_amount_unpaid_bill_reminder')** | {{ $payment->amount }} {{ $payment->currency }} |
@endcomponent
@endcomponent

{{ $outro }}

{{ $extra }}

@component('mail::button', ['url' => route('rechnung.index')])
@lang('bills.mail_link_unpaid_bill_reminder')
@endcomponent

@component('mail::subcopy')
@lang('bills.mail_footer_unpaid_bill_reminder', ['service' => config('app.name')])
@endcomponent
@endcomponent
