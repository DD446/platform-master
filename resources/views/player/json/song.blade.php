@foreach($shows as $show)
{
    "name": "{{ $show->title }}"
    ,"artist": "{{ $show->itunes['author'] ?: $show->author }}"
    ,"album": "{{ $album }}"
    ,"url": "{{ $show->_url }}"
    ,"podcast_episode_cover_art_url": "{{ $show->_image }}"
    ,"duration": "{{ $show->itunes['duration'] ?? '00:00' }}"
    ,"description": '{{ rawurlencode($show->description) }}'
{{--    "time_callbacks": {
        1: function() {
        }
    }--}}
}@if(!$loop->last),@endif
@endforeach
