@extends('main')

@section('content')
    <section class="container-fluid">
        <div class="row">
            <div class="col-12 col-lg-6">
                <img src="{{ asset('/images1/podcast-pioniere-250px.png') }}" alt="Podcast Pioniere Logo" class="img img-fluid left mr-3" style="max-height: 85px">
            </div>
            <div class="col-12 col-lg-6">
                <img src="{{ asset('/images1/audiotakes.svg') }}" alt="audiotakes Logo" class="img img-fluid right ml-3" style="max-height: 85px">
            </div>
        </div>

        <section class="space-sm">
            <div class="container">
                <div class="row justify-content-around">
                    <table class="table">
                        <tr>
                            <th>Name</th>
                            <th>E-Mail</th>
                            <th>Telefon</th>
                            <th>Podcast</th>
                        </tr>
                    @foreach($leads as $username => $lead)
                        <tr>
                            <td>{{ $lead['user']->first_name }} {{ $lead['user']->last_name }}</td>
                            <td><a href="mailto:{{ $lead['user']->email }}">{{ $lead['user']->email }}</a></td>
                            <td><a href="tel:{{ $lead['user']->telephone }}">{{ $lead['user']->telephone }}</a></td>
                            @isset($lead['redirect'])
                            <td>
                                {{ $lead['feed']->rss['title'] }}
                                <br>
                                Weiterleitung: {{$lead['redirect']}}</td>
                            @else
                            <td><a href="{{ $lead['url'] }}">{{ $lead['feed']->rss['title'] }}</a></td>
                            @endisset
                        </tr>
                    @endforeach
                    </table>
                </div>
                <!--end of row-->
            </div>
            <!--end of container-->
        </section>

    </section>
@endsection('content')
