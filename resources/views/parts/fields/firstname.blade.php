<div class="form-group {{ $errors->has('firstname') ? ' alert alert-danger' : '' }}">
    <label for="name" class="">{{$label ?? trans('fields.firstname')}}@if(isset($required) && $required == true)<span>*</span>@endif</label>
    <input id="firstname" type="text" class="form-control" name="firstname"
        placeholder="{{$placeholder ?? trans('fields.placeholder_firstname')}}"
        value="{{ old('firstname') ?? $user->firstname ?? null }}"
        @if(isset($required) && $required == true) required @endif
        @if(isset($autofocus) && $autofocus == true) autofocus @endif
        @if(isset($tabindex)) tabindex="{{$tabindex}}" @endif
        @if(isset($readonly) && $readonly === true) readonly @endif
        @if(isset($disabled) && $disabled === true) disabled @endif
    />

    @if ($errors->has('firstname'))
    <div class="mt-2">
        <span class="help-block">
            <strong>{{ $errors->first('firstname') }}</strong>
        </span>
    </div>
    @endif
</div>
