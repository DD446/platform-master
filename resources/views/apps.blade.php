@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('apps') }}
@endsection

@section('content')

    <section class="bg-gradient">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('pat.header')}}</h1>
                    <span class="title-decorative">{{trans('pat.subheader')}}</span>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    @if(in_array(auth()->user()->username, ['beispiel', 'admin']))
                    <div class="links" id="app" data-type="apps">
                        <div class="text-center">
                            <div class="spinner-grow m-5 h-1" role="status" v-if="false">
                                <span class="sr-only">@lang('package.text_loading')</span>
                            </div>
                        </div>
                        <passport-personal-access-tokens></passport-personal-access-tokens>
                        <passport-clients></passport-clients>
                        <passport-authorized-clients></passport-authorized-clients>
                    </div>
                    @else
                        <p>Der Zugriff auf das API steht aktuell noch nicht zur Verfügung.</p>
                        <p>Bitte verfolge <a href="{{route('news.index')}}">unsere News</a>, um zu erfahren, wenn das API live geht.</p>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
