@extends('main')

@section('content')
    <div class="container">

        <section class="flush-with-above">
            <div class="container">
                <h1>Anfragen verschicken</h1>
                <form action="{{ route('invitations.store') }}" method="post">
                    @csrf
                    <div class="form-group">
                        <div class="col-lg-offset-1 col-lg-3 col-md-3 col-sm-12">
                            <label for="campaigns" class="">Kampagne</label>
                        </div>
                        <div class="col-lg-7 col-md-8 col-sm-12">
{{--                            <select name="campaign_id" required id="campaigns" class="form-control form-control-lg" readonly="">
                                <option value=""></option>
                                @forelse($campaigns as $campaign)
                                    <option value="{{ $campaign->id }}" @if(request('campaign_id') == $campaign->id) selected @endif>{{ $campaign->title }}</option>
                                @empty
                                    <option value="" disabled="">Du musst zuerst eine Kampagne anlegen!</option>
                                @endforelse
                            </select>
                            <small class="help-text"><a href="{{ route('campaigns.create') }}">Kampagne anlegen</a></small>--}}
                            <input type="hidden" name="campaign_id" value="{{ $campaign->id }}">
                            <input type="text" name="" value="{{ $campaign->title }}" readonly class="form-control form-control-lg">
                        </div>
                    </div>

                    <div class="form-group">
                            @forelse($users as $user => $feeds)
                                @foreach($feeds as $feed)
                                    <div class="media">
{{--                                        @if($feed['image_id'])
                                            <img src="/podcaster/podcastermedia/action/preview/frmMediaId/{{ $feed['image_id'] }}" alt="Podcast Logo"  class="align-self-start mr-3">
                                        @endif--}}
                                        <div class="media-body">
                                            <h5 class="mt-0"><input type="checkbox" name="users[][{{ $user }}]" value="{{ $feed['id'] }}" @if($feed['disabled']) disabled  @endif /> {{ $feed['title'] }}</h5>
                                            <p>von {{ $feed['author'] }}</p>
                                            <div>{!! $feed['description'] !!}</div>
                                        </div>
                                    </div>
                                @endforeach
                            @empty
                            <div class="row">
                                <div class="col-lg-12">Keine passenden Nutzer gefunden</div>
                            </div>
                            @endforelse
                    </div>

                    <button class="btn btn-lg btn-success">Anfrage an Podcaster verschicken</button>
                </form>
            </div>
        </section>
        <!--end of container-->
    </div>
@endsection


@section('local_js')
    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection