@if(isset($hidden) && $hidden)
<input type="hidden" name="{{ $name ?? 'text' }}" value="{{ old($name) ?? $value ?? $text ?? null }}" />
@else
<div class="form-group{{ $errors->has($name ?? 'text') ? ' alert alert-danger' : '' }}">
    <div class="col-12 col-lg-12">
        <label for="{{$name ?? 'f_text'}}" class="">
            {{$label ?? null}}@if(isset($required) && $required === true)<span>*</span> @endif
        </label>
        <input id="{{$name ?? 'f_text'}}" type="{{ $type ?? 'text' }}" class="form-control" name="{{ $name ?? 'text' }}"
            @if(isset($large) && $large === true) input-lg @endif
            @if(isset($required) && $required == true) required @endif
            @if(isset($readonly) && $readonly == true) readonly @endif
            @if(isset($disabled) && $disabled === true) disabled @endif
            @if(isset($autofocus) && $autofocus == true) autofocus @endif
               value="{{ old($name) ?? $value ?? $text ?? null }}"
            @if(isset($placeholder)) placeholder="{{$placeholder}}" @endif
            @if(isset($textCount) && $textCount === true) onkeyup="window.countChar(this, 'c_{{$name ?? 'text'}}')" @endif
            @if(isset($maxLength) && is_numeric($maxLength)) maxlength="{{$maxLength}}" @endif
                />
        @if(isset($textCount) && $textCount === true)
        <div class="smallText lightgrey pull-right"><span id="c_{{$name ?? 'text'}}">0</span> @lang('message_length')</div>
        @endif
        @if ($errors->has($name ?? 'text'))
        <p class="help-block">
            <strong>{{ $errors->first($name ?? 'text') }}</strong>
        </p>
        @endif
        <div class="clearfix"></div>
        <p class="help-block">{{$help ?? null}}</p>
    </div>
</div>
@endif
