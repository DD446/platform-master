<div class="container p-4 card" id="feedback">
    <form action="{{ route('feedback.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        @include('parts.fields.text', ['hidden' => true, 'name' => 'type', 'value' => $type])
        @include('parts.fields.text', ['hidden' => true, 'name' => 'entity', 'value' => $entity])
        @include('parts.fields.comment', ['required' => true, 'label' => trans('feedback.label_comment_field'), 'autofocus' => true, 'large' => true, 'placeholder' => trans_choice('feedback.placeholder', $type)])

        <div class="form-group{{ $errors->has('screenshot') ? ' alert alert-danger' : '' }} pt-3">
            <label for="screenshot">{{ trans('feedback.label_screenshot') }} (optional)</label>
            <input type="file" name="screenshot" class="form-control-file" id="screenshot" accept="image/gif, image/jpg, image/jpeg, image/png, image/bmp">

            @if ($errors->has('screenshot'))
                <p class="help-block">
                    <strong>{{ $errors->first('screenshot') }}</strong>
                </p>
            @endif
        </div>

        <div class="pt-3 right">
            <button class="btn btn-success btn-lg">Feedback übermitteln</button>
        </div>
    </form>
</div>
