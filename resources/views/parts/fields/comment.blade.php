<div class="form-group {{ $errors->has('comment') ? ' alert alert-danger' : '' }} clearfix">
    <label for="comment" class="">
        {{$label}}@if(isset($required) && $required == true)*@endif
    </label>
    <textarea
            name="comment"
            id="comment"
            rows="10"
            class="form-control @if(isset($large) && $large === true) form-control-lg @endif"
            @if(isset($required) && $required == true) required @endif
            @if(isset($autofocus) && $autofocus == true) autofocus @endif
            @if(isset($readonly) && $readonly == true) readonly @endif
            @if(isset($disabled) && $disabled === true) disabled @endif
            @if(isset($placeholder)) placeholder="{{$placeholder}}" @endif
            @if(isset($maxLength) && is_numeric($maxLength)) maxlength="{{$maxLength}}" @endif
    >{{ old('comment') ?? $comment ?? null }}</textarea>

    @if ($errors->has('comment'))
        <span class="help-block">
            <strong>{{ $errors->first('comment') }}</strong>
        </span>
    @endif
</div>