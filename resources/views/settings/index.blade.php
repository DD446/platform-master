@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('settings') }}
@endsection

@section('content')
    @section('content')
        <section class="bg-info text-light">
            <div class="container">
                <!--end of row-->
                <div class="row">
                    <div class="col-12">
                        <h2 class="display-4">@lang('settings.header')</h2>
                        <span class="lead">@lang('settings.subheader')</span>
                    </div>
                    <!--end of col-->
                </div>
                <!--end of row-->
            </div>

            <!--end of container-->
        </section>

        <section>
            <div class="container">
                <div class="row">
                    <div class="list-group">
                        <a href="/email" class="list-group-item list-group-item-action">
                            @lang('settings.link_text_email')
                        </a>
                        <a href="/freigaben" class="list-group-item list-group-item-action">
                            @lang('settings.link_text_approvals')
                        </a>
                        <a href="{{route('accounting.index')}}" class="list-group-item list-group-item-action">
                            @lang('settings.link_text_funds')
                        </a>
                        <a href="/legacy/pakete/kuendigung" class="list-group-item list-group-item-action">
                            @lang('settings.link_text_cancel_service')
                        </a>
                        <a href="/profil" class="list-group-item list-group-item-action">
                            @lang('settings.link_text_profile')
                        </a>
                        <a href="{{route('packages')}}" class="list-group-item list-group-item-action">
                            @lang('settings.link_text_packages')
                        </a>
                        <a href="{{route('password.change')}}" class="list-group-item list-group-item-action">
                            @lang('settings.link_text_change_password')
                        </a>
                        <a href="{{route('rechnung.index')}}" class="list-group-item list-group-item-action">
                            @lang('settings.link_text_bills')
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endsection
@endsection('content')
