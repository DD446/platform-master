@extends('main')

@section('content')

{{--    <script
            class="podcaster-podcast-player"
            src="/js1/player/podcaster-podcast-player.js"
            data-configuration="{{ route('config.ppp', ['id' => 445, 'feed' => 'komdehagens']) }}"></script>

    <script
            class="podigee-podcast-player"
            src="/js1/player/podigee-podcast-player.js"
            data-configuration="{{ route('config.ppp', ['id' => 445, 'feed' => 'komdehagens']) }}"></script>
    <script
            class="podigee-podcast-player"
            src="/js1/player/podigee-podcast-player.js"
            data-configuration="/js1/player/config.js"></script>--}}

    @foreach($configs as $config)
        <a href="/player/config/{{$config->uuid}}">{{ $config->uuid }}</a> <br>
    @endforeach

@endsection('content')