@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('bill', $payment) }}
@endsection

@section('content')

    <section>
        <div class="container">
            <div class="row float-right">
                <div class="col">
                    @if(!$payment->is_refunded)
                    <a class="btn btn-primary" href="{{route('bills.download', ['id' => $payment->bill_id])}}" download>
                        @lang('bills.label_button_download_bill_as_pdf')
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section>
        @include('bills.bill')
    </section>

@endsection('content')
