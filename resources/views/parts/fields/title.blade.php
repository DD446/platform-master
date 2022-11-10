@if(isset($hidden) && $hidden)
<input type="hidden" name="title" value="{{ old('title') ?? $title ?? null }}" />
@else
<div class="form-group{{ $errors->has('title') ? ' alert alert-danger' : '' }} clearfix">
    <div class="">
        <label for="title" class="label">
            {{$label ?? trans('fields.title')}}@if(isset($required) && $required == true)*@endif
        </label>
        <input id="title" type="text" class="form-control form-control-lg" name="title"
               @if(isset($required) && $required == true) required @endif
               @if(isset($placeholder)) placeholder="{{$placeholder}}" @endif
               @if(isset($autofocus) && $autofocus == true) autofocus @endif
               value="{{ old('title') ?? $title ?? $value ?? null }}"
        />

        @if ($errors->has('title'))
        <span class="help-block">
            <strong>{{ $errors->first('title') }}</strong>
        </span>
        @endif
    </div>
</div>
@endif