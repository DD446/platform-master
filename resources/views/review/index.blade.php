@extends('main')

@section('content')
<div class="container">

    <section class="space-lg">
        <div class="container">
            <div class="row mb-4 justify-content-center text-center">
                <div class="col-12 col-md-10 col-lg-9">
                    <h1 class="display-4">Reviews</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="flush-with-above">
        <!--end of container-->
        <div class="container" id="app" data-type="review">
            <div class="list-group">
                <ul>
                    <li>Q1: Welches Problem wolltest du mit podcaster.de bewältigen?</li>
                    <li>Q2: Welche Ergebnisse liefert dir podcaster.de?</li>
                    <li>Q3: Was gefällt dir an podcaster.de am Besten?</li>
                    <li>Q4: Würdest du podcaster.de weiterempfehlen? Wenn ja, warum?</li>
                    <li>Q5: Liegt dir noch etwas auf dem Herzen?</li>
                </ul>
                @forelse($reviews as $review)
                <div class="list-group-item list-group-item-action flex-column align-items-start @if(!$review->is_public) list-group-item-light @endif @if($review->is_published) active @endif">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">{{ $review->user->first_name ?? '' }} {{ $review->user->last_name ?? '' }} ({{ $review->user->username ?? '' }} &lt;{{ $review->user->email ?? '' }}&gt;)</h5>
                        <small>
                            {{\Carbon\Carbon::createFromTimeStamp(strtotime($review->created_at))->diffForHumans()}}
                        </small>
                    </div>
                    Q1: <small>{{ $review->q1 }}</small><br>
                    Q2: <small>{{ $review->q2 }}</small><br>
                    Q3: <small>{{ $review->q3 }}</small><br>
                    Q4: <small>{{ $review->q4 }}</small><br>
                    Q5: <small>{{ $review->q5 }}</small><br>
                    Zitat:
{{--                    <form action="{{ route('admin.reviews.update', ['review' => $review->id]) }}" method="post">
                        @csrf
                        <input type="hidden" name="review_id" value="{{ $review->id }}">
                        <input type="hidden" name="_method" value="PUT">
                        <textarea name="published_cite" class="form-control form-control-lg">{{ $review->published_cite }}</textarea>
                        <button type="submit" class="btn btn-primary right">Speichern</button>
                    </form>--}}
                    <citation published_cite="{{ $review->published_cite }}" id="{{ $review->id }}" is_published="{{ $review->is_published }}"></citation>
                </div>
                @empty
                    <p>Keine Ergebnisse</p>
                @endforelse
            </div>
            {{ $reviews->links() }}
        </div>
    </section>
    <!--end of container-->
</div>
@endsection
{{--
<script>
    import Cite from "../../js/components/review/Citation";
    export default {
        components: {Cite}
    }
</script>--}}

@push('scripts')
@endpush
