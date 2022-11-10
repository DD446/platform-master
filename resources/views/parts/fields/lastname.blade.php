<div class="form-group {{ $errors->has('lastname') ? ' alert alert-danger' : '' }}">
    <label for="lastname" class="">{{$label ?? trans('fields.lastname')}}@if(isset($required) && $required == true)<span>*</span>@endif</label>
    <input id="lastname" type="text" class="form-control" name="lastname"
        placeholder="{{$placeholder ?? trans('fields.placeholder_lastname')}}"
        value="{{ old('lastname') ?? $user->lastname ?? null }}"
        @if(isset($required) && $required == true) required @endif
        @if(isset($autofocus) && $autofocus == true) autofocus @endif
        @if(isset($tabindex)) tabindex="{{$tabindex}}" @endif
        @if(isset($readonly) && $readonly === true) readonly @endif
        @if(isset($disabled) && $disabled === true) disabled @endif
        />

    @if ($errors->has('lastname'))
    <span class="help-block">
        <strong>{{ $errors->first('lastname') }}</strong>
    </span>
    @endif
</div>
