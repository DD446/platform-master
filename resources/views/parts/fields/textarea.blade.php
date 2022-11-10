@if(isset($hidden) && $hidden)
<input type="hidden" name="{{ $name ?? 'textarea' }}" value="{{ old($name) ?? $value ?? $text ?? null }}" />
@else
<div class="form-group{{ $errors->has($name ?? 'textarea') ? ' alert alert-danger' : '' }}">
    <label for="{{$name ?? 'f_text'}}" class="">{{$label ?? null}}@if(isset($required) && $required === true)<span>*</span> @endif</label>
    @if(isset($hint))
    <p class="help-block smallText lightgrey text-right">{!! $hint !!}</p>
    @endif
    <textarea id="{{$name ?? 'f_text'}}" class="form-control form-control-lg" name="{{ $name ?? 'textarea' }}"
        @if(isset($large) && $large === true) input-lg @endif
        @if(isset($required) && $required == true) required @endif
        @if(isset($readonly) && $readonly == true) readonly @endif
        @if(isset($disabled) && $disabled === true) disabled @endif
        @if(isset($autofocus) && $autofocus == true) autofocus @endif
        @if(isset($placeholder)) placeholder="{{$placeholder}}" @endif
        @if(isset($textCount) && $textCount === true) onkeyup="window.countChar(this, 'c_{{$name ?? 'textarea'}}')" @endif
        @if(isset($maxLength) && is_numeric($maxLength)) maxlength="{{$maxLength}}" @endif
        rows="10"
            >{{ old($name) ?? $value ?? $text ?? null }}</textarea>
    @if(isset($textCount) && $textCount === true)
    <div class="smallText lightgrey pull-right"><span id="c_{{$name ?? 'textarea'}}">0</span> {{trans('myjobapplication.message_length')}}</div>
    @endif
    @if ($errors->has($name ?? 'textarea'))
    <p class="help-block">
        <strong>{{ $errors->first($name ?? 'textarea') }}</strong>
    </p>
    @endif
    @isset($help)
    <div class="clearfix"></div>
    <p class="help-block">{{$help ?? null}}</p>
    @endisset
</div>
@endif
