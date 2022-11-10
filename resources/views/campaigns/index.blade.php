@extends('main')

@section('content')
    <div class="container">

        <section class="flush-with-above">
            <div class="container">
                <h1>Meine Kampagnen</h1>

                <div class="pb-5 right row clearfix">
                    <a href="{{ route('campaigns.create') }}" class="btn btn-secondary">Kampagne erstellen</a>
                </div>

                <div class="row">
                    <form action="{{ route('invitations.create') }}" method="get">
                        <div class="form-group">
                            <label for="campaign_id" class="">
                                Kampagne
                            </label>
                            <select name="campaign_id" required class="form-control form-control-lg">
                                @forelse($campaigns as $campaign)
                                    <option value="{{ $campaign->id }}">{{ $campaign->title }}</option>
                                @empty
                                    <option value="" disabled>Du musst zuerst eine Kampagne erstellen.</option>
                                @endforelse
                            </select>
                        </div>
                        <button class="btn btn-primary btn-lg">Anfragen erstellen</button>
                    </form>
                </div>
            </div>
        </section>
        <!--end of container-->
    </div>
@endsection