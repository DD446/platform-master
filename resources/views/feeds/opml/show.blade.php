@extends('opml')

@section('content')
    <outline type="rss" xmlUrl="{{$xmlUrl}}" htmlUrl="{{$htmlUrl}}" text="{{$feed->feed_id}}" title="{{$feed->rss['title']}}"/>
@endsection
