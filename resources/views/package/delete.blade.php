@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('packages_delete') }}
@endsection

@section('content')
    <div>
        <section class="bg-info text-light">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-auto text-center">
                        <h1 class="display-4">{{trans('user.header_package_delete')}}</h1>
                        <span class="title-decorative">{{trans('user.subheader_package_delete')}}</span>
                    </div>
                    <!--end of col-->
                </div>
                <!--end of row-->
            </div>
            <!--end of container-->
        </section>

        <section class="py-5 bg-white">
            <div class="bg-white">
                <div class="container">

                    @if(app('impersonate')->isImpersonating())
                        <div class="alert alert-info">
                            @lang('user.info_package_delete_not_available_for_impersonator')
                        </div>
                    @else
                        <div class="row">
                            <div class="col-12 mb-2">
                                <h2>Halt!</h2>
                                <p><strong>Paket zu teuer?</strong> Begleiche nicht nur Deine Paket-Kosten, sondern verdiene sogar Geld mit der Podcast-Vermarktung der Podcast-Pioniere.</p>
                                <p><a href="/vermarktung">Erfahre hier mehr darüber</a>, wie Du direkt über podcaster Deinen Podcast monetarisiert.</p>
                            </div>
                        </div>

                        <hr class="my-5">

                        <div class="row">
                            <div class="col-12 mb-2">
                                Bestätige das unwiderrufliche Löschen der folgenden Daten mit Absenden des Formulars:
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <form action="{{ route('user.destroy') }}" method="post">
                                    @method('DELETE')
                                    @csrf
                                    <div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" id="files" required>
                                        <label class="form-check-label ml-1" for="files">{{ trans('user.checkbox_confirm_files') }} <a href="{{ route('mediathek.index') }}"><i class="icon-link"></i></a></label>
                                    </div>
                                    <div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" id="feeds" required>
                                        <label class="form-check-label ml-1" for="feeds">{{ trans('user.checkbox_confirm_feeds') }} <a href="{{ route('feeds') }}"><i class="icon-link"></i></a></label>
                                    </div>
                                    <div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" id="stats" required>
                                        <label class="form-check-label ml-1" for="stats">{{ trans('user.checkbox_confirm_stats') }} <a href="/statistiken"><i class="icon-link"></i></a></label>
                                    </div>
                                    <div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" id="bills" required>
                                        <label class="form-check-label ml-1" for="bills">{{ trans('user.checkbox_confirm_bills') }} <a href="{{ route('rechnung.index') }}"><i class="icon-link"></i></a></label>
                                    </div>
                                    <div class="form-group">
                                        <label for="pwd">{{ trans('user.label_enter_your_password') }}</label>
                                        <input type="password" class="form-control form-control-lg" id="pwd" name="password" required placeholder="{{ trans('user.placeholder_enter_password') }}">
                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                                <span class="text-danger">{{ $errors->first('password') }}</span>
                                            </span>
                                        @endif
                                    </div>
                                    <button type="submit" class="btn btn-danger">{{ trans('user.button_confirm_delete') }}</button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection
