@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('funds') }}
@endsection

@section('content')

    <section class="bg-info text-white">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col">
                    <div class="media align-items-center">
                        <div class="media-body">
                            <div class="mb-3">
                                <h1 class="h2 mb-2">@lang('accounting.header_funds') <span class="badge @if(auth()->user()->funds < 0) badge-danger @else badge-success @endif">{{ auth()->user()->funds }}&euro;</span></h1>
                                <span>@lang('accounting.description_funds')</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end of col-->
                <div class="col-auto">
                    <a href="{{ route('accounting.create') }}">
                        <button class="btn btn-podcaster"><i
                                    class="icon-credit-card mr-1"></i> @lang('accounting.link_add_funds')</button>
                    </a>
                    <a href="{{ route('rechnung.index') }}"><button class="btn btn-secondary">@lang('accounting.link_bills')</button></a>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>

    <section class="">
        <div class="container card p-4">
            <div class="row">
                <div class="col-12 m-3">
                    <h3>{{ trans('accounting.header_bookings') }}</h3>

                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class="row font-weight-bolder">
                                <div class="col-2">
                                    {{ trans('accounting.title_booking_date') }}
                                </div>
                                <div class="col-7">
                                    {{ trans('accounting.title_booking_description') }}
                                </div>
                                <div class="col-3 text-right">
                                    {{ trans('accounting.title_booking_amount') }}
                                </div>
                            </div>
                        </li>

                        @forelse($bookings as $booking)
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-2">
                                        {{$booking->date_created->diffForHumans()}}
                                    </div>
                                    <div class="col-7">
                                        {{ $booking->activity_description }}
                                        @if($booking->activity_type<>0)
                                            @if(in_array($booking->activity_type, [\App\Classes\Activity::PACKAGE, \App\Classes\Activity::EXTRAS]))
                                                ({{ $booking->date_start->format('d.m.Y') }} - {{ $booking->date_end->format('d.m.Y') }})
                                            @endif
                                        @endif
                                    </div>
                                    <div class="col-3 text-right">
                                        {{ $booking->amount }} {{ $booking->currency }}
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item">
                                @lang('accounting.empty_no_bookings')
                            </li>
                        @endforelse
                    </ul>

                    <div class="mt-2">
                        {{ $bookings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection('content')
