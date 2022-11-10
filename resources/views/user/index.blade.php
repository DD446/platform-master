@php
    session()->has('auth') ||
    Str::startsWith(session('status'), ['two-factor', 'recovery']) ? $securityIsActive = 'active' : $securityIsActive = '';
@endphp

@if (session('status') === 'two-factor-authentication-enabled')
    {{ session()->flash('status', 'Bitte vervollständige die Schritte zur Einrichtung der Zwei-Faktor-Authentifizierung.') }}
@elseif(session('status') === 'two-factor-authentication-disabled')
    {{ session()->flash('status', 'Du hast die Zwei-Faktor-Authentifizierung erfolgreich deaktiviert.') }}
@php
    session()->remove('auth');
@endphp
@elseif (session('status') == 'two-factor-authentication-confirmed')
    {{ session()->flash('status', 'Du hast die Zwei-Faktor-Authentifizierung erfolgreich eingerichtet.') }}
@php
    session()->remove('auth');
@endphp
@elseif (session('status') == 'recovery-codes-generated')
    {{ session()->flash('status', 'Es wurden neue Wiederherstellungs-Codes generiert.') }}
@endif

@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('profile') }}
@endsection

@section('content')
    <div>
        <section class="bg-info text-light">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-auto text-center">
                        <h1 class="display-4">{{trans('user.header')}}</h1>
                        <span class="title-decorative">{{trans('user.subheader')}}</span>
                    </div>
                    <!--end of col-->
                </div>
                <!--end of row-->
            </div>
            <!--end of container-->
        </section>

        <section class="flush-with-above space-0" id="app" data-type="profile">
            <div class="bg-white">
                <div class="container">
                    <div class="row">
                        <div class="col-12 pt-4">
                            <div class="text-center" v-if="false">
                                <div class="spinner-border m-5" style="width: 3rem; height: 3rem;" role="status">
                                    <span class="sr-only">...</span>
                                </div>
                            </div>
                            <b-tabs class="nav nav-tabs" v-cloak>

                                <!-- TAB ACCOUNT OVERVIEW -->
                                <b-tab :title="$t('profile.tab_general')">
                                    <div class="container m-lg-5 m-1 mt-3 mb-5">
                                        <div class="table-responsive-sm">
                                            <table class="table">
                                                <tr>
                                                    <th scope="row"
                                                        class="font-weight-bold">@lang('profile.login_email'):
                                                    </th>
                                                    <td>{{ auth()->user()->email }}</td>
                                                    <td><a href="{{ route('email.index') }}">ändern</a></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row" class="font-weight-bold">@lang('profile.approvals')
                                                        :
                                                    </th>
                                                    <td>
                                                        @forelse(auth()->user()->approvals as $service)
                                                            <span class="badge badge-dark p-1">
                                                        {{ $service['screen_name'] }} ({{ ucfirst($service['service']) }})
                                                        </span>
                                                        @empty
                                                            keine
                                                        @endforelse
                                                    </td>
                                                    <td><a href="/freigaben">bearbeiten</a></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row" class="font-weight-bold">@lang('profile.funds'):
                                                    </th>
                                                    <td>
                                                        <funds :amount="{{ auth()->user()->funds }}"></funds>
                                                    </td>
                                                    <td><a href="{{ route('accounting.create') }}">aufladen</a></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row" class="font-weight-bold">@lang('profile.package'):
                                                    </th>
                                                    <td>
                                                        {{ trans_choice('package.package_name', auth()->user()->package->package_name) }}
                                                        @if(auth()->user()->new_package_id)
                                                            @php
                                                                $newPackage = \App\Models\Package::find(auth()->user()->new_package_id);
                                                            @endphp
                                                            ({{ trans('user.package_downgrade_saved', ['name' =>
                            trans_choice('package.package_name', $newPackage->package_name)]) }})
                                                        @endif
                                                    </td>
                                                    <td><a href="{{ route('packages') }}">wechseln</a></td>
                                                </tr>
                                            </table>
                                        </div>

                                        <div class="row">
                                            @if(auth()->user()->isInTrial())
                                                <div class="col-auto text-right">
                                                    <a href="{{ route('accounting.create') }}" class="btn btn-success">Konto
                                                        aktivieren (Probephase beenden)</a>
                                                </div>
                                            @endif
                                            <div class="col-auto text-right">
                                                <a href="{{ route('package.delete') }}" class="btn btn-danger">
                                                    @lang('profile.link_text_cancel_service')
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </b-tab>

                                <!-- TAB CONTACT INFORMATION -->
                                <b-tab :title="$t('profile.tab_contact')">
                                    <div class="mb-5">
                                        <profile-address :user="{{ auth()->user() }}"></profile-address>
                                    </div>
                                </b-tab>

                                <!-- TAB ACCOUNT SECURITY-->
                                <b-tab :title="$t('profile.tab_security')" {{$securityIsActive}}>
                                    <div class="container m-lg-5 m-1 mt-3 mb-5">
                                        <!--two-factor-authentication-->
                                        <div class="card w-100 mb-2">
                                            <div class="card-header text-white bg-info">
                                                @lang('profile.2fa')
                                                <i class="icon icon-info-with-circle" v-b-popover.hover.click="'@lang('profile.2fa_tooltip')'"></i>
                                            </div>

                                            <!-- if it is enabled -->
                                            @if (auth()->user()->two_factor_confirmed_at && auth()->user()->two_factor_secret)
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item">
                                                        <form method="POST" action="/user/two-factor-authentication">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="col-12 my-2  col-lg-9 col-md-8">
                                                                    @lang('profile.2fa_description_enabled')
                                                                </div>
                                                                <div class="col-6 my-2 col-lg-3 col-md-4">
                                                                    @method('DELETE')
                                                                    <div class="custom-control custom-switch">
                                                                        <input type="checkbox" class="custom-control-input" id="2fa_enabled" checked onchange="if(confirm('Möchtest du wirklich die Zwei-Faktor-Authentifizierung deaktivieren?')){submit()}">
                                                                        <label class="custom-control-label" for="2fa_enabled">@lang('profile.2fa_enabled')</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </li>
                                                </ul>

                                                <!-- if it is in activation process -->
                                            @elseif (auth()->user()->two_factor_secret)
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item">
                                                        <div class="row p-2">
                                                            <div class="col-12 col-lg-3 col-md-6 px-md-4 py-md-2">
                                                                <form method="POST" action="/user/two-factor-recovery-codes">
                                                                    @csrf
                                                                    <div class="my-2">
                                                                        @lang('profile.save_recovery_codes')
                                                                        <i class="icon icon-info-with-circle text-blue" v-b-popover.hover.click="'@lang('profile.recovery_codes_tooltip')'"></i>
                                                                    </div>
                                                                    <div class="my-2">
                                                                        <code>
                                                                            @foreach(auth()->user()->recoveryCodes() as $recoveryCode)
                                                                                {{ $recoveryCode }}<br>
                                                                            @endforeach
                                                                        </code>
                                                                    </div>
                                                                    <div class="my-2">
                                                                        <button type="submit"
                                                                                class="btn btn-primary btn-block my-2">
                                                                            @lang('profile.generate_new')
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                            <div class="col-12 col-lg-3 col-md-6 px-md-4 py-md-2">
                                                                <div class="my-2">
                                                                    @lang('profile.2fa_install_app')
                                                                    <i class="icon icon-info-with-circle text-blue" v-b-popover.hover.click="'@lang('profile.auth_apps_tooltip')'"></i>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 col-lg-3 col-md-6 px-md-4 py-md-2">
                                                                <div class="my-2">
                                                                    @lang('profile.2fa_scan_qr_code')</div>
                                                                <div class="my-2">{!! auth()->user()->twoFactorQrCodeSvg() !!}</div>
                                                            </div>
                                                            <div class="col-12 col-lg-3 col-md-6 px-md-4 py-md-2">
                                                                <form method="POST"
                                                                      action="/user/confirmed-two-factor-authentication">
                                                                    @csrf
                                                                    <div class="my-2">
                                                                        @lang('profile.2fa_enter_confirm_code')</div>
                                                                    <input type="text" class="form-control my-2"
                                                                           id="code" name="code"
                                                                           placeholder="Code" autofocus>
                                                                    <button type="submit"
                                                                            class="btn btn-primary btn-block my-2">
                                                                        @lang('profile.2fa_submit_code_button')
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                        <!-- abort activation process -->
                                                    </li>
                                                    <li class="list-group-item">
                                                        <form method="POST" action="/user/two-factor-authentication">
                                                            @csrf
                                                            <div class="row p-2">
                                                                <div class="col-0 col-lg-3 "></div>
                                                                <div class="col-0 col-lg-3 "></div>
                                                                <div class="col-0 col-lg-3 "></div>
                                                                <div class="col-12 col-md-6 col-lg-3">
                                                                    <div class="my-2">
                                                                        <div class="my-2">@lang('profile.2fa_abort_process')</div>
                                                                        <div class="my-2">
                                                                            @method('DELETE')
                                                                            <button class="btn btn-danger btn-block">@lang('profile.2fa_disable')</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </li>
                                                </ul>
                                        </div>

                                        <!-- if it is disabled -->
                                        @else
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item">
                                                    <form method="POST" action="/user/two-factor-authentication">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col-12 my-2  col-lg-9 col-md-8">
                                                                @lang('profile.2fa_description_disabled')
                                                            </div>
                                                            <div class="col-6 my-2 col-lg-3 col-md-4">
                                                                <div class="custom-control custom-switch">
                                                                    <input type="checkbox" class="custom-control-input" id="2fa_disabled" onchange="submit()">
                                                                    <label class="custom-control-label" for="2fa_disabled">@lang('profile.2fa_disabled')</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </li>
                                            </ul>
                                        @endif

                                    </div>

                                </b-tab>
                            </b-tabs>
                        </div>
                        <!--end of col-->
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->
            </div>
        </section>
    </div>
@endsection('content')
