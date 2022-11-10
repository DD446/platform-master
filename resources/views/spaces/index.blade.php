@extends('main')

@section('content')

    <section class="bg-gradient">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('spaces.header')}}</h1>
                    <span class="title-decorative">{{trans('spaces.subheader')}}</span>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>

    <section class="container bg-white my-5 p-5">

        <div class="row mt-3">
            <div class="col-12">
                <h2>{{trans('spaces.header_calculations')}}</h2>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <dl>
                    <dt class="">@lang('spaces.start_date_billing_period')</dt><dd>{{$accountingTimes['startTimeFormatted']}}</dd>
                    <dt>@lang('spaces.start_date_next_billing_period')</dt><dd>{{$accountingTimes['nextTimeFormatted']}}</dd>
                </dl>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-8">
                @lang('spaces.available_space')
            </div>
            <div class="col-4">
                {{$availableSpace}} ({{ $space }})
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-8 pl-3">
                @lang('spaces.available_space_package')
            </div>
            <div class="col-4">
                {{$availableSpacePackage}} ({{ $spacePackage }})
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-8 pl-3">
                @lang('spaces.available_space_extras')
            </div>
            <div class="col-4">
                {{$availableSpaceExtras}} ({{ $spaceExtras }})
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <h3>@lang('spaces.header_calculation_details')</h3>
            </div>
            <div class="col-12 mt-3">
                <table class="table">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">@lang('spaces.table_header_filename')</th>
                            <th scope="col">@lang('spaces.table_header_created')</th>
                            <th scope="col">@lang('spaces.table_header_filesize')</th>
                            <th scope="col">@lang('spaces.table_header_spaceused')</th>
                            <th scope="col">@lang('spaces.table_header_spacetype')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($userUploads as $upload)
                        <tr>
                            <td>{{ $upload->file_name }}</td>
                            <td>{{ $upload->created_at->diffForHumans() }}</td>
                            <td>{{ $upload->humanFriendlySize }}</td>
                            <td>{{ $upload->humanFriendlySpaceUsed }}</td>
                            <td>
                                {{ trans_choice('spaces.space_type', $upload->space->type) }}
                                @if($upload->space->type === 2)
                                    ({{$upload->space->humanFriendlySpace}}, {{$upload->space->localizedDate}})
                                @endif
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    @lang('spaces.table_no_uploads')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $userUploads->links() }}
    </section>
@endsection
