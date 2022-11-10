@extends('main')

@section('content')
<div class="row" id="app" data-type="subscribers">
    @foreach($data as $id => $a)
        <div class="col-xl-1 col-lg-1 col-md-3 col-sm-4">
            <div class="card text-center">
                <div class="sprite-container">
                    <div class="{{ $id }}-icon"></div>
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ array_key_first($a) }}</h5>
                    <div class="card-text display-4" title="{{ trans_choice('main.external_subscribers', head($a), ['count' => head($a)]) }}">
                        @if($id == 'podcaster')
                            <div>
                                <subscribers action="/podcaster/podcaster/action/getSubscribers"></subscribers>
                            </div>
                        @else
                            {{ head($a) }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection